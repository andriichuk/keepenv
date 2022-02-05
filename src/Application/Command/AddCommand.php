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
        $specWriter = $specWriterFactory->basedOnResource((string) $input->getOption('spec'));

        $specReaderFactory = new SpecReaderFactory();
        $specReader = $specReaderFactory->basedOnSource((string) $input->getOption('spec'));

        $manager = new AddNewVariableManager(
            new EnvFileWriter(new EnvFileManager((string) $input->getOption('target-env-file'))),
            $specReader,
            $specWriter,
        );

        $wantToAddMoreVariables = true;

        while ($wantToAddMoreVariables) {
            $name = (string) $io->ask('Please enter variable name');
            $name = str_replace(' ', '_', trim($name));
            $description = (string) $io->ask('Enter variable description');
            $required = $io->confirm('Is the variable required?', true);
            $export = $io->confirm('Should contain `export` keyword?', false);
            $system = $io->confirm('Is it a system variable (from $_ENV or $_SERVER)?', false);
            $type = $this->askForType($io);
            $value = $system ? null : $this->askForValue($io);

            $variable = new Variable(
                $name,
                $description,
                $export,
                $system,
                array_filter(['required' => $required] + $type),
            );

            $variableIsValid = $variable->system;

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
                    $value = $this->askForValue($io);
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

    private function askForType(SymfonyStyle $io): array
    {
        $type = (string) $io->choice(
            'Select variable type',
            ['string', 'numeric', 'boolean', 'enum'],
            0
        );

        if ($type !== 'enum') {
            return [$type => true];
        }

        $addMoreOptions = true;
        $options = [];

        while ($addMoreOptions) {
            $options[] = (string) $io->ask('Enter enum option');
            $addMoreOptions = $io->confirm('Want to add more options?', true);
        }

        return ['enum' => array_unique($options)];
    }

    private function askForValue(SymfonyStyle $io): string
    {
        return (string) $io->ask('Enter variable value');
    }
}
