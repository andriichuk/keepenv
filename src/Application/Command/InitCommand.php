<?php

declare(strict_types=1);

namespace Andriichuk\Enviro\Application\Command;

use Andriichuk\Enviro\Specification\EnvVariables;
use Andriichuk\Enviro\Specification\Specification;
use Andriichuk\Enviro\Specification\Variable;
use Andriichuk\Enviro\Writer\Specification\SpecificationWriterFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class InitCommand extends Command
{
    protected static $defaultName = 'init';

    protected function configure(): void
    {
        $this
            ->addOption('env', 'e', InputOption::VALUE_REQUIRED, 'Target environment name.')
            ->addOption('target', 't', InputOption::VALUE_REQUIRED, 'Source file.')
            ->setDescription('Application environment verification.')
            ->setHelp('This command allows you to verify environment specification.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $variables = \Dotenv\Dotenv::createArrayBacked(dirname(__DIR__, 3) . '/stubs', '.env.demo')->load();

        $specification = new Specification('1.0');
        $envSpecification = new EnvVariables($input->getOption('env'));

        foreach ($variables as $key => $value) {
            $required = trim((string) $value) === '';
            $rules = $this->guessType($value);

            if ($required) {
                $rules['required'] = true;
            }

            $envSpecification->add(
                new Variable(
                    $key,
                    $this->toSentence($key),
                     $rules
                )
            );
        }

        $specification->add($envSpecification);

        $writer = (new SpecificationWriterFactory())->basedOnFileExtension($input->getOption('target'));
        $writer->write($input->getOption('target'), $specification);

        return Command::SUCCESS;
    }

    private function toSentence(string $key): string
    {
        $sentence = str_replace('_', ' ', strtolower($key));

        $replace = [
            'app' => 'application',
            'env' => 'environment',
            'aws' => 'AWS',
            'url' => 'URL',
            'api' => 'API',
            'id' => 'ID',
            'dsn' => 'DSN',
            'js' => 'JS',
            'CSS' => 'CSS',
            'db' => 'database',
            's3' => 'S3',
            'log' => 'Logging', // Loggingin
        ];

        return ucfirst(str_replace(array_keys($replace), array_values($replace), $sentence)) . '.';
    }

    private function guessType(string $value): array
    {
        if (is_numeric($value)) {
            return ['numeric' => true];
        }

        $boolean = in_array(strtolower($value), ["true", "false", "on", "1", "yes", "off", "0", "no"], true);

        if ($boolean) {
            return ['enum' => ['On', 'Off']];
        }

        return ['string' => true];
    }
}
