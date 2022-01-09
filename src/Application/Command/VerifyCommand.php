<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Application\Command;

use Andriichuk\KeepEnv\Environment\Loader\EnvFileLoaderFactory;
use Andriichuk\KeepEnv\Specification\Reader\SpecificationReaderFactory;
use Andriichuk\KeepEnv\Verification\SpecVerificationService;
use Andriichuk\KeepEnv\Validation\EmailValidator;
use Andriichuk\KeepEnv\Validation\EnumValidator;
use Andriichuk\KeepEnv\Validation\EqualsValidator;
use Andriichuk\KeepEnv\Validation\IntegerValidator;
use Andriichuk\KeepEnv\Validation\RequiredValidator;
use Andriichuk\KeepEnv\Validation\ValidatorRegistry;
use Andriichuk\KeepEnv\Verification\VariableVerification;
use Andriichuk\KeepEnv\Verification\VerificationReport;
use Andriichuk\KeepEnv\Environment\Writer\EnvFileWriter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class VerifyCommand extends Command
{
    protected static $defaultName = 'verify';

    protected function configure(): void
    {
        $this
            ->addArgument('env', InputArgument::REQUIRED, 'The name of the environment to be verified.')
            ->addOption('env-file', 'ef', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Dotenv file path to check.', ['./'])
            ->addOption('spec', 's', InputOption::VALUE_REQUIRED, 'Dotenv specification file path.', 'env.spec.yaml')
            ->setDescription('Application environment verification.')
            ->setHelp('This command allows you to verify environment variables according to specification.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $specReaderFactory = new SpecificationReaderFactory();
        $envLoaderFactory = new EnvFileLoaderFactory();

        $service = new SpecVerificationService(
            $specReaderFactory->basedOnResource($input->getOption('spec')),
            $envLoaderFactory->baseOnAvailability(),
            new VariableVerification(ValidatorRegistry::default()),
        );

        $io = new SymfonyStyle($input, $output);
        $io->title("Start checking the content of the file...");
        $files = implode(', ', $input->getOption('env-file'));
        $io->listing([
            "Environment name: <info>{$input->getArgument('env')}</info>.",
            "Environment file: <info>$files</info>.",
            "Environment specification: <info>{$input->getOption('spec')}</info>.",
        ]);

        try {
            $verificationReport = $service->verify(
                $input->getArgument('env'),
                $input->getOption('env-file'),
                $input->getOption('spec'),
            );
        } catch (Throwable $exception) {
            $io->error($exception->getMessage());

            return Command::FAILURE;
        }

        if (!$verificationReport->isEmpty()) {
            $this->renderMessages($verificationReport, $io);

            return Command::FAILURE;
        }

        $io->success('Application environment is valid.');

        return Command::SUCCESS;
    }

    private function renderMessages(VerificationReport $reports, SymfonyStyle $io): void
    {
        $rows = [];
        $variables = [];

        foreach ($reports->all() as $report) {
            $formattedVariable = "<bg=red;options=bold>$report->variable</>";

            if (isset($variables[$report->variable])) {
                $formattedVariable = '';
            }

            $variables[$report->variable] = true;
            $rows[] = [$formattedVariable, $report->message];
        }

        $io->text("<options=bold>Found {$reports->count()} errors:</>");
        $io->table(['Variable', 'Message'], $rows);
        $io->error('Application environment is not valid.');
    }
}
