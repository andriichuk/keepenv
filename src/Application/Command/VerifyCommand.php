<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Application\Command;

use Andriichuk\KeepEnv\Environment\Loader\EnvFileLoaderFactory;
use Andriichuk\KeepEnv\Specification\Reader\SpecificationReaderFactory;
use Andriichuk\KeepEnv\Verification\SpecVerificationService;
use Andriichuk\KeepEnv\Validation\ValidatorRegistry;
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
                'spec',
                's',
                InputOption::VALUE_REQUIRED,
                'Dotenv specification file path.',
                './env.spec.yaml',
            )
            ->setDescription('Application environment verification.')
            ->setHelp('This command allows you to verify environment variables according to the specification.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Starting to verify environment variables...');
        $envFilePaths = implode(', ', $input->getOption('env-file'));
        $io->listing([
            "Environment name: <info>{$input->getArgument('env')}</info>",
            "Dotenv file paths: <info>$envFilePaths</info>",
            "Specification file path: <info>{$input->getOption('spec')}</info>",
        ]);

        $specReaderFactory = new SpecificationReaderFactory();
        $envLoaderFactory = new EnvFileLoaderFactory();

        $service = new SpecVerificationService(
            $specReaderFactory->basedOnResource($input->getOption('spec')),
            $envLoaderFactory->baseOnAvailability(),
            new VariableVerification(ValidatorRegistry::default()),
        );

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

        $count = $reports->count();

        $io->text(
            sprintf(
                '<options=bold>Found %d error%s:</>',
                $count,
                $count > 1 ? 's' : '',
            )
        );
        $io->table(['Variable', 'Message'], $rows);
        $io->error('Application environment is not valid.');
    }
}
