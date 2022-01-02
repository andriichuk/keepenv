<?php

declare(strict_types=1);

namespace Andriichuk\Enviro\Application\Command;

use Andriichuk\Enviro\Specification\Reader\SpecificationReaderFactory;
use Andriichuk\Enviro\Environment\Writer\EnvFileWriter;
use Andriichuk\Enviro\Specification\Writer\SpecificationWriterFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class RemoveCommand extends Command
{
    protected static $defaultName = 'remove';

    protected function configure(): void
    {
        $this
            ->addArgument('variable', InputArgument::REQUIRED, 'Variable name')
            ->addOption('env', 'e', InputOption::VALUE_REQUIRED, 'Target environment name.')
            ->addOption('env-file', 'ef', InputOption::VALUE_REQUIRED, 'Target environment name.')
            ->addOption('env-spec', 'es', InputOption::VALUE_REQUIRED, 'Spec file.')
            ->setDescription('Application environment verification.')
            ->setHelp('This command allows you to verify environment specification.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $factory = new SpecificationReaderFactory();
        $reader = $factory->basedOnResource($input->getOption('env-spec'));

        $writerFactory = new SpecificationWriterFactory();
        $writer = $writerFactory->basedOnResource($input->getOption('env-spec'));
        $specification = $reader->read($input->getOption('env-spec'));
        $envSpec = $specification->get($input->getOption('env'));

        if (!$envSpec->has($input->getArgument('variable'))) {
            $output->writeln('Variable does not exists.');

            return Command::FAILURE;
        }

        $envSpec->remove($input->getArgument('variable'));
        $specification->add($envSpec);
        $writer->write($input->getOption('env-spec'), $specification);

        $envFileWriter = new EnvFileWriter($input->getOption('env-file'));
        $envFileWriter->remove($input->getArgument('variable'));

        return Command::SUCCESS;
    }
}
