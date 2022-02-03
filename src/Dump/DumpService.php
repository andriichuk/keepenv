<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Dump;

use Andriichuk\KeepEnv\Dump\Exceptions\EnvFileAlreadyExistsException;
use Andriichuk\KeepEnv\Environment\Loader\EnvLoaderInterface;
use Andriichuk\KeepEnv\Environment\Writer\EnvFileManagerInterface;
use Andriichuk\KeepEnv\Environment\Writer\EnvWriterInterface;
use Andriichuk\KeepEnv\Specification\Reader\SpecReaderInterface;
use Andriichuk\KeepEnv\Utils\Stringify;
use JsonException;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class DumpService
{
    private SpecReaderInterface $specReader;
    private EnvLoaderInterface $envLoader;
    private EnvFileManagerInterface $envFileManager;
    private EnvWriterInterface $envWriter;
    private Stringify $stringify;

    public function __construct(
        SpecReaderInterface $specReader,
        EnvLoaderInterface $envLoader,
        EnvFileManagerInterface $envFileManager,
        EnvWriterInterface $envWriter,
        Stringify $stringify
    ) {
        $this->specReader = $specReader;
        $this->envLoader = $envLoader;
        $this->envFileManager = $envFileManager;
        $this->envWriter = $envWriter;
        $this->stringify = $stringify;
    }

    public function dump(
        string $envName,
        array $envPaths,
        string $specFile,
        bool $withValues = false,
        bool $overrideExistingFile = false
    ): void {
        if (!$overrideExistingFile && $this->envFileManager->exists()) {
            throw new EnvFileAlreadyExistsException();
        }

        $this->envFileManager->createIfNotExists();

        $variablesFromState = [];

        if ($withValues) {
            $variablesFromState = $this->envLoader->load($envPaths, true);
        }

        $envSpec = $this->specReader->read($specFile)->get($envName);

        /** @var array<string, string> $variablesToWrite */
        $variablesToWrite = [];

        foreach ($envSpec->all() as $variable) {
            /** @var mixed $value */
            $value = $variablesFromState[$variable->name] ?? $variable->default;

            try {
                $value = $value !== null
                    ? $this->stringify->format($value)
                    : '';
            } catch (JsonException $exception) {
                $value = '';
            }

            $variablesToWrite[$variable->name] = $value;
        }

        $this->envWriter->addBatch($variablesToWrite);
    }
}
