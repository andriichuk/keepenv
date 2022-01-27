<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Application\Command;

use Andriichuk\KeepEnv\Environment\Reader\EnvReaderFactory;
use Andriichuk\KeepEnv\Filling\EnvFileFillingService;
use Andriichuk\KeepEnv\Specification\Reader\SpecificationReaderFactory;
use Andriichuk\KeepEnv\Environment\Writer\EnvFileWriter;
use Andriichuk\KeepEnv\Specification\Variable;
use Andriichuk\KeepEnv\Validation\RulesRegistry;
use Andriichuk\KeepEnv\Verification\VariableVerification;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class FillCommand extends Command
{
    protected static $defaultName = 'fill';

    protected function configure(): void
    {
        $this
            ->addOption('env', 'e', InputOption::VALUE_REQUIRED, 'The name of the environment to be initiated.', 'common')
            ->addOption('target-env-file', 'tef', InputOption::VALUE_REQUIRED, 'Dotenv file path to check.', '.env')
            ->addOption('env-file', 'ef', InputOption::VALUE_REQUIRED, 'Dotenv file path to check.', './')
            ->addOption('env-reader', 'ep', InputOption::VALUE_REQUIRED, 'Environment reader.', 'auto')
            ->addOption('spec', 's', InputOption::VALUE_REQUIRED, 'Dotenv specification file path.', 'env.spec.yaml')
            ->setDescription('Application environment verification.')
            ->setHelp('This command allows you to verify environment specification.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Variables filling');

        $specReaderFactory = new SpecificationReaderFactory();
        $envReaderFactory = new EnvReaderFactory();

        $service = new EnvFileFillingService(
            $specReaderFactory->basedOnSource((string) $input->getOption('spec')),
            $envReaderFactory->make((string) $input->getOption('env-reader')),
            new EnvFileWriter($input->getOption('target-env-file')),
            new VariableVerification(RulesRegistry::default()),
        );
        $service->fill(
            (string) $input->getOption('env'),
            (string) $input->getOption('env-file'),
            (string) $input->getOption('spec'),
            /**
             * @return mixed
             */
            static function (Variable $variable, callable $validator) use ($io) {
                if (isset($variable->rules['enum'])) {
                    return $io->choice(
                        "Please select value for the key `$variable->name`: ",
                        $variable->rules['enum'],
                        $variable->default ?? null,
                    );
                }

                return $io->ask(
                    "Please enter value for key `$variable->name`: ",
                    $variable->default ?? null,
                    $validator,
                );
            },
            static function (string $message) use ($io): void {
                if ($message !== '') {
                    $io->text($message);
                }
            },
        );

        $io->success('All variables were successfully filled.');

        return Command::SUCCESS;
    }
}
