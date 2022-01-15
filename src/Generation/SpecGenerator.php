<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Generation;

use Andriichuk\KeepEnv\Environment\Reader\EnvReaderInterface;
use Andriichuk\KeepEnv\Generation\Presets\PresetFactory;
use Andriichuk\KeepEnv\Specification\EnvVariables;
use Andriichuk\KeepEnv\Specification\Specification;
use Andriichuk\KeepEnv\Specification\Variable;
use Andriichuk\KeepEnv\Specification\Writer\SpecWriterInterface;
use RuntimeException;

class SpecGenerator
{
    private EnvReaderInterface $envReader;
    private SpecWriterInterface $specWriter;
    private PresetFactory $presetFactory;

    public function __construct(EnvReaderInterface $envReader, SpecWriterInterface $specWriter, PresetFactory $presetFactory)
    {
        $this->envReader = $envReader;
        $this->specWriter = $specWriter;
        $this->presetFactory = $presetFactory;
    }

    public function generate(string $env, array $envPaths, string $targetSpecPath, callable $shouldOverrideSpec, ?string $presetAlias = null): void
    {
        if (file_exists($targetSpecPath)) {
            $override = (bool) $shouldOverrideSpec();

            if (!$override) {
                throw new RuntimeException('Specification file already exists and was not modified.');
            }
        }

        $variables = $this->envReader->read($envPaths);

        $spec = Specification::default();
        $envSpec = new EnvVariables($env);

        $preset = [];

        if ($presetAlias !== null) {
            $preset = $this->presetFactory->make($presetAlias)->provide();
        }

        foreach ($variables as $key => $value) {
            if (isset($preset[$key])) {
                $envSpec->add($preset[$key]);

                continue;
            }

            $required = trim((string) $value) === '';
            $rules = $this->guessType($key, $value ?? '');

            if ($required) {
                $rules['required'] = true;
            }

            $envSpec->add(
                new Variable(
                    $key,
                    $this->toSentence($key),
                    false,
                    false,
                    $rules,
                    null,
                )
            );
        }

        $spec->add($envSpec);

        $this->specWriter->write($targetSpecPath, $spec);
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

    private function guessType(string $key, string $value): array
    {
        if ($key === 'APP_ENV') {
            $environments = ['dev', 'develop', 'local', 'test', 'testing', 'stage', 'staging', 'prod', 'production'];
            $index = array_search($value, $environments, true);

            if ($index !== false) {
                return [
                    'enum' => [$environments[$index]],
                ];
            }
        }

        if (is_numeric($value)) {
            return ['numeric' => true];
        }

        $boolean = $this->guessBooleanType($value);

        if ($boolean !== null) {
            return $boolean;
        }

        return ['string' => true];
    }

    public function guessBooleanType(string $value): ?array
    {
        $value = strtolower($value);

        if (in_array($value, ['true', 'false'], true)) {
            return ['enum' => ['true', 'false']];
        }

        if (in_array($value, ['on', 'off'], true)) {
            return ['enum' => ['on', 'off']];
        }

        if (in_array($value, ['yes', 'no'], true)) {
            return ['enum' => ['yes', 'no']];
        }

        if (in_array($value, ['1', '0'], true)) {
            return ['enum' => ['1', '0']];
        }

        return null;
    }
}
