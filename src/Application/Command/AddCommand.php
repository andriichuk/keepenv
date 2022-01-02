<?php

declare(strict_types=1);

namespace Andriichuk\Enviro\Application\Command;

use Andriichuk\Enviro\Manager\AddNewVariableManager;
use Andriichuk\Enviro\Manager\AddVariableCommand;
use Andriichuk\Enviro\Specification\Reader\SpecificationReaderFactory;
use Andriichuk\Enviro\Specification\Variable;
use Andriichuk\Enviro\Environment\Writer\EnvFileWriter;
use Andriichuk\Enviro\Specification\Writer\SpecificationWriterFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class AddCommand extends Command
{
    protected static $defaultName = 'add';

    protected function configure(): void
    {
        $this
            ->addOption('env',  'e', InputOption::VALUE_REQUIRED, 'Target environment name.')
            ->addOption('env-file', 'ef', InputOption::VALUE_REQUIRED, '.env file path.')
            ->addOption('spec-file', 'sf', InputOption::VALUE_REQUIRED, '.env spec file path.')
            ->setDescription('Application environment verification.')
            ->setHelp('This command allows you to verify environment specification.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = $this->askForName($input, $output);
        $description = $this->askForDescription($input, $output);
        $required = $this->askForRequired($input, $output);
        $type = $this->askForType($input, $output);
        $value = $this->askForValue($input, $output);

        $variable = new Variable($name, $description, [
            $type,
        ], $required);

        $writerFactory = new SpecificationWriterFactory();
        $readerFactory = new SpecificationReaderFactory();

        $manager = new AddNewVariableManager(
            new EnvFileWriter($input->getOption('env-file')),
            $readerFactory->basedOnResource($input->getOption('spec-file')),
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

    private function askForRequired(InputInterface $input, OutputInterface $output): bool
    {
        $helper = $this->getHelper('question');
        $requiredQuestion = new ConfirmationQuestion('Required?', true);

        return $helper->ask($input, $output, $requiredQuestion);
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
