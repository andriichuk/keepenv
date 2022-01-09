<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Application\Command;

use Andriichuk\KeepEnv\Environment\Loader\EnvFileLoaderFactory;
use Andriichuk\KeepEnv\Environment\Provider\EnvStateProvider;
use Andriichuk\KeepEnv\Specification\SpecificationGenerator;
use Andriichuk\KeepEnv\Specification\Writer\SpecificationWriterFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class InitCommand extends Command
{
    protected static $defaultName = 'init';

    protected function configure(): void
    {
        $this
            ->addOption('env', 'e', InputOption::VALUE_REQUIRED, 'The name of the environment to be initiated.', 'common')
            ->addOption('env-file', 'ef', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Dotenv file path to check.', ['./'])
            ->addOption('spec', 's', InputOption::VALUE_REQUIRED, 'Dotenv specification file path.', 'env.spec.yaml')
            ->setDescription('Application environment verification.')
            ->setHelp('This command allows you to verify environment specification.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Start generating a new specification based on environment.');
        $envFiles = implode(', ', $input->getOption('env-file'));
        $io->listing([
            "Environment name: <info>{$input->getOption('env')}</info>.",
            "Environment files: <info>{$envFiles}</info>.",
            "Environment specification: <info>{$input->getOption('spec')}</info>.",
        ]);

        $override = false;

        if (file_exists($input->getOption('spec'))) {
            $override = $io->confirm('Specification file already exists. Do you want to override it?', $override);

            if (!$override) {
                $io->warning('Specification file was not modified.');

                return Command::FAILURE;
            }
        }

        $loaderFactory = new EnvFileLoaderFactory();
        $writerFactory = new SpecificationWriterFactory();

        $generator = new SpecificationGenerator(
            $loaderFactory->baseOnAvailability(),
            new EnvStateProvider(),
            $writerFactory->basedOnResource($input->getOption('spec'))
        );

        $generator->generate(
            $input->getOption('env'),
            $input->getOption('env-file'),
            $input->getOption('spec'),
            $override,
        );

        $io->success("Environment specification was successfully created.");

        return Command::SUCCESS;
    }
}
