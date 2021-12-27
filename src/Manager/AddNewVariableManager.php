<?php

declare(strict_types=1);

namespace Andriichuk\Enviro\Manager;

use Andriichuk\Enviro\Reader\Specification\SpecificationReaderInterface;
use Andriichuk\Enviro\Writer\Env\EnvFileWriter;
use Andriichuk\Enviro\Writer\Specification\SpecificationWriterInterface;
use InvalidArgumentException;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class AddNewVariableManager
{
    private EnvFileWriter $envFileWriter;
    private SpecificationReaderInterface $specificationReader;
    private SpecificationWriterInterface $specificationWriter;

    public function __construct(
        EnvFileWriter $envFileWriter,
        SpecificationReaderInterface $specificationReader,
        SpecificationWriterInterface $specificationWriter
    ) {
        $this->envFileWriter = $envFileWriter;
        $this->specificationReader = $specificationReader;
        $this->specificationWriter = $specificationWriter;
    }

    public function add(AddVariableCommand $command): void
    {
        $specification = $this->specificationReader->read($command->specificationFilePath);
        $envSpecification = $specification->get($command->environment);

        if ($envSpecification->get($command->variable->name)) {
            throw new InvalidArgumentException("Variable with name `{$command->variable->name} already defined.`");
        }

        $envSpecification->set($command->variable);
        $specification->add($envSpecification);

        $this->envFileWriter->add($command->variable->name, $command->value);

        $this->specificationWriter->write($command->specificationFilePath, $specification);
    }
}
