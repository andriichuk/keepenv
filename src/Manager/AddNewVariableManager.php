<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Manager;

use Andriichuk\KeepEnv\Specification\Reader\SpecReaderInterface;
use Andriichuk\KeepEnv\Environment\Writer\EnvFileWriter;
use Andriichuk\KeepEnv\Specification\Writer\SpecWriterInterface;
use InvalidArgumentException;

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

    public function add(AddVariableCommand $command): void
    {
        $specification = $this->specReader->read($command->specificationFilePath);
        $envSpecification = $specification->get($command->environment);

        if ($envSpecification->get($command->variable->name)) {
            throw new InvalidArgumentException("Variable with name `{$command->variable->name} already defined.`");
        }

        $envSpecification->add($command->variable);
        $specification->add($envSpecification);

        $this->envFileWriter->add($command->variable->name, $command->value);

        $this->specWriter->write($command->specificationFilePath, $specification);
    }
}
