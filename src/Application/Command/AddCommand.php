<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Application\Command;

use Andriichuk\KeepEnv\Manager\AddNewVariableManager;
use Andriichuk\KeepEnv\Manager\AddVariableCommand;
use Andriichuk\KeepEnv\Specification\Reader\SpecificationReaderFactory;
use Andriichuk\KeepEnv\Specification\Variable;
use Andriichuk\KeepEnv\Environment\Writer\EnvFileWriter;
use Andriichuk\KeepEnv\Specification\Writer\SpecWriterFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class AddCommand extends Command
{
    protected static $defaultName = 'add';

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

        $name = $this->askForName($input, $output);
        $description = $this->askForDescription($input, $output);
        $required = $io->confirm('Variable is required?', true);
        $export = $io->confirm('Should `contain` export keyword?', false);
        $system = $io->confirm('Variable is system?', false);
        $type = $this->askForType($input, $output);
        $value = $this->askForValue($input, $output);

        // TODO check default
        $variable = new Variable(
            $name,
            $description,
            $export,
            $system,
            array_filter([$type]),
            $required,
        );

        $writerFactory = new SpecWriterFactory();
        $readerFactory = new SpecificationReaderFactory();

        $manager = new AddNewVariableManager(
            new EnvFileWriter($input->getOption('env-file')),
            $readerFactory->basedOnSource($input->getOption('spec-file')),
            $writerFactory->basedOnResource($input->getOption('spec-file')),
        );

        $manager->add(
            new AddVariableCommand(
                $variable,
                $value,
                $input->getOption('env'),
                $input->getOption('env-file'),
                $input->getOption('spec-file'),
            )
        );

        return Command::SUCCESS;
    }

    private function askForName(InputInterface $input, OutputInterface $output): string
    {
        $helper = $this->getHelper('question');
        $question = new Question('Please enter variable name: ');
        $question->setNormalizer(static function (string $value): string {
            return mb_strtoupper(str_replace(' ', '_', trim($value)));
        });
        $question->setMaxAttempts(2);

        return $helper->ask($input, $output, $question);
    }

    private function askForDescription(InputInterface $input, OutputInterface $output): string
    {
        $helper = $this->getHelper('question');
        $question = new Question('Please enter variable description: ');
        $question->setNormalizer(static function (string $value): string {
            return trim($value);
        });
        $question->setMaxAttempts(2);

        return $helper->ask($input, $output, $question);
    }

    private function askForType(InputInterface $input, OutputInterface $output): string
    {
        $helper = $this->getHelper('question');
        $question = new ChoiceQuestion(
            'Please select variable type',
            ['string', 'int', 'boolean', 'enum'],
            0
        );
        $question->setErrorMessage('Type %s is invalid.');

        return $helper->ask($input, $output, $question);
    }

    private function askForValue(InputInterface $input, OutputInterface $output): string
    {
        $helper = $this->getHelper('question');
        $question = new Question('Please enter value: ');
        $question->setNormalizer(static function (string $value): string {
            return trim($value);
        });
        $question->setMaxAttempts(2);

        return $helper->ask($input, $output, $question);
    }
}
