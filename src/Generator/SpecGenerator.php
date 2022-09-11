<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Generator;

use Andriichuk\KeepEnv\Environment\Reader\EnvReaderInterface;
use Andriichuk\KeepEnv\Generator\Exceptions\SpecGeneratorException;
use Andriichuk\KeepEnv\Generator\Presets\PresetFactory;
use Andriichuk\KeepEnv\Specification\EnvVariables;
use Andriichuk\KeepEnv\Specification\Specification;
use Andriichuk\KeepEnv\Specification\Variable;
use Andriichuk\KeepEnv\Specification\Writer\SpecWriterInterface;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class SpecGenerator
{
    private EnvReaderInterface $envReader;
    private SpecWriterInterface $specWriter;
    private PresetFactory $presetFactory;

    public function __construct(
        EnvReaderInterface $envReader,
        SpecWriterInterface $specWriter,
        PresetFactory $presetFactory
    ) {
        $this->envReader = $envReader;
        $this->specWriter = $specWriter;
        $this->presetFactory = $presetFactory;
    }

    public function generate(
        string $envName,
        array $envPaths,
        string $targetSpecPath,
        callable $shouldOverrideSpec,
        ?string $presetAlias = null
    ): void {
        if (file_exists($targetSpecPath)) {
            $override = (bool) $shouldOverrideSpec();

            if (!$override) {
                throw SpecGeneratorException::alreadyExists();
            }
        }

        $variables = $this->envReader->read(...$envPaths);

        $spec = Specification::default();
        $envSpec = new EnvVariables($envName);

        $preset = [];

        if ($presetAlias !== null) {
            $preset = $this->presetFactory->make($presetAlias)->provide();
        }

        /**
         * @var string $key
         * @var mixed $value
         */
        foreach ($variables as $key => $value) {
            if (isset($preset[$key])) {
                $envSpec->add($preset[$key]);

                continue;
            }

            $rules = [
                'required' => trim((string) $value) !== ''
            ];
            $rules = array_merge($rules, $this->guessType($key, $value));

            $envSpec->add(
                new Variable($key, $this->toSentence($key), false, false, $rules, null)
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
        ];

        return ucfirst(str_replace(array_keys($replace), array_values($replace), $sentence)) . '.';
    }

    /**
     * @param mixed $value
     */
    private function guessType(string $key, $value): array
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

        if (is_bool($value) || (!empty($value) && filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) !== null)) {
            return ['boolean' => true];
        }

        return [];
    }
}
