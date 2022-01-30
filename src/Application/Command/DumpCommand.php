<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Application\Command;

use Andriichuk\KeepEnv\Dump\DumpService;
use Andriichuk\KeepEnv\Environment\Loader\EnvLoaderFactory;
use Andriichuk\KeepEnv\Environment\Writer\EnvFileManager;
use Andriichuk\KeepEnv\Environment\Writer\EnvFileWriter;
use Andriichuk\KeepEnv\Specification\Reader\SpecificationReaderFactory;
use Andriichuk\KeepEnv\Utils\Stringify;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class DumpCommand extends Command
{
    protected static $defaultName = 'dump';

    protected function configure(): void
    {
        $this
            ->addOption('env', 'e', InputOption::VALUE_REQUIRED, 'The name of the environment to be filled.', 'common')
            ->addOption('target-env-file', 't', InputOption::VALUE_REQUIRED, 'DotEnv file path for filling.', './.env')
            ->addOption('env-file', 'f', InputOption::VALUE_REQUIRED, 'DotEnv file path for reading variable values.', './')
            ->addOption('env-reader', 'r', InputOption::VALUE_REQUIRED, 'Environment reader.', 'auto')
            ->addOption('env-provider', 'p', InputOption::VALUE_REQUIRED, 'Application environment state provider.', 'auto')
            ->addOption('spec', 's', InputOption::VALUE_REQUIRED, 'DotEnv specification file path.', 'keepenv.yaml')
            ->addOption('with-values', 'wv', InputOption::VALUE_REQUIRED, 'DotEnv specification file path.', false)
            ->setDescription('Application environment variables filling.')
            ->setHelp('This command allows you to fill empty environment variables according to specification.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Dump environment state to the file.');

        $targetEnvFile = (string) $input->getOption('target-env-file');

        $envFileManager = new EnvFileManager((string) $input->getOption('target-env-file'));
        $override = false;

        if ($envFileManager->exists()) {
            $override = $io->confirm('Environment file already exists. Do you want to override it?', false);

            if (!$override) {
                $io->warning('Environment file already exists and was not modified.');

                return Command::FAILURE;
            }
        }

        $specReaderFactory = new SpecificationReaderFactory();
        $envLoaderFactory = new EnvLoaderFactory();

        $specFile = (string) $input->getOption('spec');

        $service = new DumpService(
            $specReaderFactory->basedOnSource($specFile),
            $envLoaderFactory->make((string) $input->getOption('env-provider')),
            $envFileManager,
            new EnvFileWriter($envFileManager),
            new Stringify(),
        );

        $service->dump(
            (string) $input->getOption('env'),
            (array) $input->getOption('env-file'),
            (string) $input->getOption('spec'),
            (bool) $input->getOption('with-values'),
            $override,
        );

        $fullPath = realpath($targetEnvFile);
        $io->success("Environment file was successfully generated.\nFile path [$fullPath]");

        return Command::SUCCESS;
    }
}
