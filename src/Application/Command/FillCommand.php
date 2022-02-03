<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Application\Command;

use Andriichuk\KeepEnv\Environment\Reader\EnvReaderFactory;
use Andriichuk\KeepEnv\Environment\Writer\EnvFileManager;
use Andriichuk\KeepEnv\Environment\Writer\EnvFileWriter;
use Andriichuk\KeepEnv\Filling\EnvFileFillingService;
use Andriichuk\KeepEnv\Specification\Reader\SpecReaderFactory;
use Andriichuk\KeepEnv\Specification\Variable;
use Andriichuk\KeepEnv\Utils\Stringify;
use Andriichuk\KeepEnv\Validation\Rules\RulesRegistry;
use Andriichuk\KeepEnv\Validation\VariableValidation;
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
            ->addOption('env', 'e', InputOption::VALUE_REQUIRED, 'The name of the environment to be filled.', 'common')
            ->addOption('target-env-file', 't', InputOption::VALUE_REQUIRED, 'DotEnv file path for filling.', './.env')
            ->addOption('env-file', 'f', InputOption::VALUE_REQUIRED, 'DotEnv file path for reading variable values.', './')
            ->addOption('env-reader', 'r', InputOption::VALUE_REQUIRED, 'Environment reader.', 'auto')
            ->addOption('spec', 's', InputOption::VALUE_REQUIRED, 'DotEnv specification file path.', 'keepenv.yaml')
            ->setDescription('Application environment variables filling.')
            ->setHelp('This command allows you to fill empty environment variables according to specification.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Environment variables filling and validating.');

        $specReaderFactory = new SpecReaderFactory();
        $envReaderFactory = new EnvReaderFactory();

        $specFile = (string) $input->getOption('spec');
        $variableValuePresenter = new Stringify();

        $service = new EnvFileFillingService(
            $specReaderFactory->basedOnSource($specFile),
            $envReaderFactory->make((string) $input->getOption('env-reader')),
            new EnvFileWriter(new EnvFileManager((string) $input->getOption('target-env-file'))),
            new VariableValidation(RulesRegistry::default()),
        );
        $countOfFilledVariables = $service->fill(
            (string) $input->getOption('env'),
            (string) $input->getOption('env-file'),
            $specFile,
            /**
             * @return mixed
             */
            static function (Variable $variable, callable $validator) use ($io, $variableValuePresenter) {
                if (isset($variable->rules['enum']) && is_array($variable->rules['enum'])) {
                    return $io->choice(
                        "Please select value for the key `$variable->name`: ",
                        $variable->rules['enum'],
                        $variable->default !== null
                            ? $variableValuePresenter->format($variable->default)
                            : null,
                    );
                }

                return $io->ask(
                    "Please enter value for key `$variable->name`: ",
                    $variable->default !== null
                        ? $variableValuePresenter->format($variable->default)
                        : null,
                    $validator,
                );
            },
            static function (string $message) use ($io): void {
                if ($message !== '') {
                    $io->text($message);
                }
            },
        );

        if ($countOfFilledVariables === 0) {
            $io->success('All variables are already filled.');
        } else {
            $io->success("All variables [$countOfFilledVariables] were successfully filled.");
        }

        return Command::SUCCESS;
    }
}
