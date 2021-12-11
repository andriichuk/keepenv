<?php

declare(strict_types=1);

namespace Andriichuk\EnvValidator\Application\Command;

use Andriichuk\EnvValidator\EnvSpecReader;
use Andriichuk\EnvValidator\EnvStateProvider;
use Andriichuk\EnvValidator\Verification\SpecVerificationService;
use Andriichuk\EnvValidator\Validation\EmailValidator;
use Andriichuk\EnvValidator\Validation\EnumValidator;
use Andriichuk\EnvValidator\Validation\EqualsValidator;
use Andriichuk\EnvValidator\Validation\IntegerValidator;
use Andriichuk\EnvValidator\Validation\RequiredValidator;
use Andriichuk\EnvValidator\Validation\ValidatorRegistry;
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

        $specification = require dirname(__DIR__, 3) . '/stubs/env.spec.php';

        $parser = new SpecVerificationService(
            new EnvStateProvider(),
            new EnvSpecReader($specification),
            $validatorRegistry,
        );

        $messages = $parser->verify($input->getArgument('env'));

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
