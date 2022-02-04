<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Manager;

use Andriichuk\KeepEnv\Manager\Exceptions\NewVariablesManagerException;
use Andriichuk\KeepEnv\Specification\Reader\SpecReaderInterface;
use Andriichuk\KeepEnv\Environment\Writer\EnvFileWriter;
use Andriichuk\KeepEnv\Specification\Variable;
use Andriichuk\KeepEnv\Specification\Writer\SpecWriterInterface;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class AddNewVariableManager
{
    private EnvFileWriter $envFileWriter;
    private SpecReaderInterface $specReader;
    private SpecWriterInterface $specWriter;

    public function __construct(
        EnvFileWriter $envFileWriter,
        SpecReaderInterface $specReader,
        SpecWriterInterface $specWriter
    ) {
        $this->envFileWriter = $envFileWriter;
        $this->specReader = $specReader;
        $this->specWriter = $specWriter;
    }

    public function add(Variable $variable, string $value, string $environment, string $specificationFilePath): void
    {
        $spec = $this->specReader->read($specificationFilePath);
        $envSpec = $spec->get($environment);

        if ($envSpec->get($variable->name)) {
            throw NewVariablesManagerException::variableAlreadyDefined($variable->name);
        }

        $envSpec->add($variable);
        $spec->add($envSpec);

        $this->envFileWriter->add($variable->name, $value);
        $this->specWriter->write($specificationFilePath, $spec);
    }
}
