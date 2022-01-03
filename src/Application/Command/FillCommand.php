<?php

declare(strict_types=1);

namespace Andriichuk\Enviro\Application\Command;

use Andriichuk\Enviro\Environment\Loader\EnvFileLoaderFactory;
use Andriichuk\Enviro\Filling\EnvFileFillingService;
use Andriichuk\Enviro\Specification\Reader\SpecificationReaderFactory;
use Andriichuk\Enviro\Environment\Writer\EnvFileWriter;
use Andriichuk\Enviro\Specification\Variable;
use Andriichuk\Enviro\Validation\ValidatorRegistry;
use Andriichuk\Enviro\Verification\VariableVerification;
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
            ->addOption('env-file', 'ef', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Dotenv file path to check.', ['./'])
            ->addOption('spec', 's', InputOption::VALUE_REQUIRED, 'Dotenv specification file path.', 'env.spec.yaml')
            ->setDescription('Application environment verification.')
            ->setHelp('This command allows you to verify environment specification.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Variables filling');

        $specReaderFactory = new SpecificationReaderFactory();
        $envLoaderFactory = new EnvFileLoaderFactory();

        $service = new EnvFileFillingService(
            $specReaderFactory->basedOnResource($input->getOption('spec')),
            $envLoaderFactory->baseOnAvailability(),
            new EnvFileWriter($input->getOption('target-env-file')),
            new VariableVerification(ValidatorRegistry::default()),
        );
        $service->fill(
            $input->getOption('env'),
            $input->getOption('env-file'),
            $input->getOption('spec'),
            /**
             * @return mixed
             */
            static function (Variable $variable, callable $validator) use ($io) {
                if (isset($variable->rules['enum'])) {
                    return $io->choice(
                        "Please select value for the key `$variable->name`: ",
                        $variable->rules['enum'],
                        $variable->rules['default'] ?? null,
                    );
                }

                return $io->ask(
                    "Please enter value for key `$variable->name`: ",
                    $variable->rules['default'] ?? null,
                    $validator,
                );
            },
            static function (string $message) use ($io): void {
                if ($message !== '') {
                    $io->text($message);
                }
            },
        );

        return Command::SUCCESS;
    }
}
