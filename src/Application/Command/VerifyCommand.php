<?php

declare(strict_types=1);

namespace Andriichuk\Enviro\Application\Command;

use Andriichuk\Enviro\Reader\Specification\ReaderFactory;
use Andriichuk\Enviro\State\EnvStateProvider;
use Andriichuk\Enviro\Verification\SpecVerificationService;
use Andriichuk\Enviro\Validation\EmailValidator;
use Andriichuk\Enviro\Validation\EnumValidator;
use Andriichuk\Enviro\Validation\EqualsValidator;
use Andriichuk\Enviro\Validation\IntegerValidator;
use Andriichuk\Enviro\Validation\RequiredValidator;
use Andriichuk\Enviro\Validation\ValidatorRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class VerifyCommand extends Command
{
    protected static $defaultName = 'verify';

    protected function configure(): void
    {
        $this
            ->addArgument('env', InputArgument::REQUIRED, 'Target environment name.')
            ->addArgument('source', InputArgument::REQUIRED, 'Source file.')
            ->setDescription('Application environment verification.')
            ->setHelp('This command allows you to verify environment specification.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $validatorRegistry = new ValidatorRegistry();
        $validatorRegistry->add(new IntegerValidator());
        $validatorRegistry->add(new EmailValidator());
        $validatorRegistry->add(new EnumValidator());
        $validatorRegistry->add(new EqualsValidator());
        $validatorRegistry->add(new RequiredValidator());

        $source = $input->getArgument('source');
        $sourcePath = dirname(__DIR__, 3) . '/stubs/' . $source;

        $factory = new ReaderFactory();
        $reader = $factory->basedOnFileExtension($sourcePath);

        $service = new SpecVerificationService(new EnvStateProvider(), $reader, $validatorRegistry);
        $messages = $service->verify($sourcePath, $input->getArgument('env'));

        if ($messages !== []) {
            $output->writeln('<error>Application environment is not valid.</error>');

            foreach ($messages as $message) {
                $output->writeln("<error>$message</error>");
            }

            return Command::FAILURE;
        }

        $output->writeln('<info>Application environment is valid.</info>');

        return Command::SUCCESS;
    }
}