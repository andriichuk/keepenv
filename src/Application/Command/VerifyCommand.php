<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Application\Command;

use Andriichuk\KeepEnv\Application\Command\Utils\CommandHeader;
use Andriichuk\KeepEnv\Environment\Loader\EnvLoaderFactory;
use Andriichuk\KeepEnv\Specification\Reader\SpecificationReaderFactory;
use Andriichuk\KeepEnv\Validation\RulesRegistry;
use Andriichuk\KeepEnv\Verification\SpecVerificationService;
use Andriichuk\KeepEnv\Verification\VariableVerification;
use Andriichuk\KeepEnv\Verification\VerificationReport;
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
            ->addArgument('env', InputArgument::REQUIRED, 'Environment name to verify.')
            ->addOption(
                'env-file',
                'ef',
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'Dotenv file paths to check.',
                ['./'],
            )
            ->addOption(
                'env-overwrite',
                'eo',
                InputOption::VALUE_REQUIRED,
                'Dotenv file paths to check.',
                false,
            )
            ->addOption(
                'env-provider',
                'p',
                InputOption::VALUE_REQUIRED,
                'Application environment state provider.',
                'auto'
            )
            ->addOption(
                'spec',
                's',
                InputOption::VALUE_REQUIRED,
                'Dotenv specification file path.',
                './keepenv.yaml',
            )
            ->addOption(
                'override-system-vars',
                'o',
                InputOption::VALUE_REQUIRED,
                'Flag for overriding system variables.',
                false,
            )
            ->setDescription('Application environment verification.')
            ->setHelp('This command allows you to verify environment variables according to the specification.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $envName = (string) $input->getArgument('env');
        $envFiles = (array) $input->getOption('env-file');
        $specFile = (string) $input->getOption('spec');

        $header = new CommandHeader($io);
        $header->display('Starting to verify environment variables...', $envName, $envFiles, $specFile);

        $specReaderFactory = new SpecificationReaderFactory();
        $envLoaderFactory = new EnvLoaderFactory();

        $service = new SpecVerificationService(
            $specReaderFactory->basedOnSource($specFile),
            $envLoaderFactory->make((string) $input->getOption('env-provider')),
            new VariableVerification(RulesRegistry::default()),
        );

        try {
            $verificationReport = $service->verify(
                $envName,
                $envFiles,
                $specFile,
                (bool) $input->getOption('override-system-vars'),
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

        $errorsCount = $reports->errorsCount();
        $variablesCount = $reports->variablesCount();

        $io->text(
            sprintf(
                'Checked <options=bold>%d</> variable%s. Found <options=bold>%d</> error%s:',
                $reports->variablesCount(),
                $variablesCount > 1 ? 's' : '',
                $errorsCount,
                $errorsCount > 1 ? 's' : '',
            )
        );
        $io->table(['Variable', 'Message'], $rows);
        $io->error('Application environment is not valid.');
    }
}
