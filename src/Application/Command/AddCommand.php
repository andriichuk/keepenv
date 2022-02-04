<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Application\Command;

use Andriichuk\KeepEnv\Environment\Writer\EnvFileManager;
use Andriichuk\KeepEnv\Manager\AddNewVariableManager;
use Andriichuk\KeepEnv\Manager\Exceptions\NewVariablesManagerException;
use Andriichuk\KeepEnv\Specification\Reader\SpecReaderFactory;
use Andriichuk\KeepEnv\Specification\Variable;
use Andriichuk\KeepEnv\Environment\Writer\EnvFileWriter;
use Andriichuk\KeepEnv\Specification\Writer\SpecWriterFactory;
use Andriichuk\KeepEnv\Validation\Rules\RulesRegistry;
use Andriichuk\KeepEnv\Validation\VariableReport;
use Andriichuk\KeepEnv\Validation\VariableValidation;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
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
        $io->title('Adding a new variable');

        $rulesRegistry = RulesRegistry::default();
        $validator = new VariableValidation($rulesRegistry);

        $specWriterFactory = new SpecWriterFactory();
        $specWriter = $specWriterFactory->basedOnResource($input->getOption('spec'));

        $specReaderFactory = new SpecReaderFactory();
        $specReader = $specReaderFactory->basedOnSource($input->getOption('spec'));

        $manager = new AddNewVariableManager(
            new EnvFileWriter(new EnvFileManager($input->getOption('target-env-file'))),
            $specReader,
            $specWriter,
        );

        $wantToAddMoreVariables = true;

        while ($wantToAddMoreVariables) {
            $name = $this->askForName($input, $output);
            $description = $this->askForDescription($input, $output);
            $required = $io->confirm('Is the variable required?', true);
            $export = $io->confirm('Should contain `export` keyword?', false);
            $system = $io->confirm('Is it a system variable (from $_ENV or $_SERVER)?', false);
            $type = $this->askForType($input, $output);
            $value = $this->askForValue($input, $output);

            $variable = new Variable(
                $name,
                $description,
                $export,
                $system,
                array_filter(['required' => $required] + $type),
            );

            $variableIsValid = false;

            while (!$variableIsValid) {
                $report = $validator->validate($variable, $value);
                $variableIsValid = $report === [];

                if (!$variableIsValid) {
                    $io->error('Variable value is not valid:');
                    $io->listing(
                        array_map(
                            static fn (VariableReport $variableReport): string => $variableReport->message,
                            $report,
                        ),
                    );
                    $value = $this->askForValue($input, $output);
                }
            }

            try {
                $manager->add(
                    $variable,
                    $value,
                    (string) $input->getOption('env'),
                    (string) $input->getOption('spec'),
                );

                $io->success('Variable was successfully added to specification.');
                $io->text(
                    sprintf(
                        'List of available rules that you can manually add to variable: %s.',
                        implode(', ', $rulesRegistry->listOfAliases(['string', 'numeric', 'boolean', 'enum', 'required']))
                    ),
                );
            } catch (NewVariablesManagerException $exception) {
                $io->warning($exception->getMessage());
            } finally {
                $wantToAddMoreVariables = $io->confirm('Do you want to add more variables?', false);
            }
        }

        return Command::SUCCESS;
    }

    private function askForName(InputInterface $input, OutputInterface $output): string
    {
        $helper = $this->getHelper('question');
        $question = new Question('Please enter variable name: ');
        $question->setNormalizer(static function (string $value): string {
            return str_replace(' ', '_', trim($value));
        });
        $question->setMaxAttempts(2);

        return $helper->ask($input, $output, $question);
    }

    private function askForDescription(InputInterface $input, OutputInterface $output): string
    {
        $helper = $this->getHelper('question');
        $question = new Question('Enter variable description: ');
        $question->setNormalizer(static function (string $value): string {
            return trim($value);
        });
        $question->setMaxAttempts(2);

        return $helper->ask($input, $output, $question);
    }

    private function askForType(InputInterface $input, OutputInterface $output): array
    {
        $helper = $this->getHelper('question');
        $question = new ChoiceQuestion(
            'Select variable type',
            ['string', 'numeric', 'boolean', 'enum'],
            0
        );
        $question->setErrorMessage('Type %s is invalid.');

        $type = $helper->ask($input, $output, $question);

        if ($type !== 'enum') {
            return [$type => true];
        }

        $addMoreOptions = true;
        $options = [];

        while ($addMoreOptions) {
            $enumQuestion = new Question('Enter enum option: ');
            $options[] = $helper->ask($input, $output, $enumQuestion);

            $confirm = new ConfirmationQuestion('Want to add more options?', true);
            $addMoreOptions = $helper->ask($input, $output, $confirm);
        }

        return ['enum' => array_unique($options)];
    }

    private function askForValue(InputInterface $input, OutputInterface $output): string
    {
        $helper = $this->getHelper('question');
        $question = new Question('Enter variable value: ');
        $question->setNormalizer(static function (string $value): string {
            return trim($value);
        });
        $question->setMaxAttempts(2);

        return $helper->ask($input, $output, $question);
    }
}
