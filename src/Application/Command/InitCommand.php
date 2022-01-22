<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Application\Command;

use Andriichuk\KeepEnv\Application\Command\Utils\CommandHeader;
use Andriichuk\KeepEnv\Environment\Reader\EnvReaderFactory;
use Andriichuk\KeepEnv\Generator\Presets\PresetFactory;
use Andriichuk\KeepEnv\Generator\SpecGenerator;
use Andriichuk\KeepEnv\Specification\Writer\SpecWriterFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class InitCommand extends Command
{
    protected static $defaultName = 'init';

    protected function configure(): void
    {
        $this
            ->addOption(
                'env',
                'e',
                InputOption::VALUE_REQUIRED,
                'Target environment name.',
                'common',
            )
            ->addOption(
                'env-file',
                'f',
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'Paths to the dotenv files, based on which the specification will be generated.',
                ['./'],
            )
            ->addOption(
                'env-reader',
                'r',
                InputOption::VALUE_REQUIRED,
                'Reader name.',
                'auto',
            )
            ->addOption(
                'spec',
                's',
                InputOption::VALUE_REQUIRED,
                'Path to new dotenv specification file.',
                'env.spec.yaml',
            )
            ->addOption(
                'preset',
                'p',
                InputOption::VALUE_REQUIRED,
                'Preset alias.',
            )
            ->setDescription('Application environment specification generation.')
            ->setHelp('This command allows you to generate specification based on dotenv file variables.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $envName = (string) $input->getOption('env');
        $envFiles = (array) $input->getOption('env-file');
        $specFile = (string) $input->getOption('spec');
        $preset = is_string($input->getOption('preset')) ? $input->getOption('preset') : null;

        $header = new CommandHeader($io);
        $header->display('Starting to generate a new specification based on environment files...', $envName, $envFiles, $specFile);

        $envReaderFactory = new EnvReaderFactory();
        $writerFactory = new SpecWriterFactory();

        try {
            $generator = new SpecGenerator(
                $envReaderFactory->make((string) $input->getOption('env-reader')),
                $writerFactory->basedOnResource($specFile),
                new PresetFactory(),
            );

            $generator->generate(
                $envName,
                $envFiles,
                $specFile,
                static function () use ($io): bool {
                    return $io->confirm('Specification file already exists. Do you want to override it?', false);
                },
                $preset,
            );

            $io->success("Environment specification was successfully created.");

            return Command::SUCCESS;
        } catch (Throwable $exception) {
            $io->error($exception->getMessage());

            return Command::FAILURE;
        }
    }
}
