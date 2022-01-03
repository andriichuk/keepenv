<?php

declare(strict_types=1);

namespace Andriichuk\Enviro\Specification;

use Andriichuk\Enviro\Environment\Loader\EnvFileLoaderInterface;
use Andriichuk\Enviro\Environment\Provider\EnvStateProviderInterface;
use Andriichuk\Enviro\Specification\Writer\SpecificationWriterInterface;
use RuntimeException;

class SpecificationGenerator
{
    private EnvFileLoaderInterface $envFileLoader;
    private EnvStateProviderInterface $envStateProvider;
    private SpecificationWriterInterface $specificationWriter;

    public function __construct(
        EnvFileLoaderInterface $envFileLoader,
        EnvStateProviderInterface $envStateProvider,
        SpecificationWriterInterface $specificationWriter
    ) {
        $this->envFileLoader = $envFileLoader;
        $this->envStateProvider = $envStateProvider;
        $this->specificationWriter = $specificationWriter;
    }

    public function generate(string $env, array $envPaths, string $targetSpecPath, bool $override): void
    {
        if (!$override && file_exists($targetSpecPath)) {
            throw new RuntimeException('Spec file already exists.');
        }

        $variables = $this->envFileLoader->load($envPaths);

        $specification = new Specification('1.0');
        $envSpecification = new EnvVariables($env);

        foreach ($variables as $key => $value) {
            $required = trim((string) $value) === '';
            $rules = $this->guessType($value ?? '');

            if ($required) {
                $rules['required'] = true;
            }

            $envSpecification->add(
                new Variable(
                    $key,
                    $this->toSentence($key),
                    false,
                    $rules,
                    null,
                )
            );
        }

        $specification->add($envSpecification);

        $this->specificationWriter->write($targetSpecPath, $specification);
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
