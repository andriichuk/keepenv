<?php

declare(strict_types=1);

namespace Andriichuk\Enviro\Application\Command;

use Andriichuk\Enviro\Reader\Specification\SpecificationReaderFactory;
use Andriichuk\Enviro\Writer\Env\EnvFileWriter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class FillCommand extends Command
{
    protected static $defaultName = 'fill';

    protected function configure(): void
    {
        $this
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
        $specification = $reader->read($input->getOption('env-spec'));
        $envSpec = $specification->get($input->getOption('env'));
        $envFileWriter = new EnvFileWriter($input->getOption('env-file'));

        foreach ($envSpec->all() as $envSpec) {
            $envFileWriter->save($envSpec->name, $this->askForValue($envSpec->name, $input, $output));
        }

        return Command::SUCCESS;
    }

    private function askForValue(string $key, InputInterface $input, OutputInterface $output): string
    {
        $helper = $this->getHelper('question');
        $question = new Question("Please enter value for key <info>`$key`</info>: ");
        $question->setNormalizer(static function (string $value): string {
            return trim($value);
        });
        $question->setMaxAttempts(2);

        return $helper->ask($input, $output, $question);
    }
}
