<?php

declare(strict_types=1);

namespace Andriichuk\Enviro\Manager;

use Andriichuk\Enviro\Reader\Specification\SpecificationReaderInterface;
use Andriichuk\Enviro\State\EnvStateProvider;
use Andriichuk\Enviro\Writer\Env\EnvFileWriter;
use Andriichuk\Enviro\Writer\Specification\SpecificationWriterInterface;

class AddNewVariableManager
{
    private EnvStateProvider $envStateProvider;
    private EnvFileWriter $envFileWriter;
    private SpecificationReaderInterface $specificationReader;
    private SpecificationWriterInterface $specificationWriter;

    public function __construct(
        EnvStateProvider $envStateProvider,
        EnvFileWriter $envFileWriter,
        SpecificationReaderInterface $specificationReader,
        SpecificationWriterInterface $specificationWriter
    ) {
        $this->envStateProvider = $envStateProvider;
        $this->envFileWriter = $envFileWriter;
        $this->specificationReader = $specificationReader;
        $this->specificationWriter = $specificationWriter;
    }

    public function add(AddVariableCommand $command)
    {
        $specification = $this->specificationReader->read($command->specificationFilePath);

        $envSpecification = $specification->get($command->environment);

        if ($envSpecification->get($command->variable->name)) {
            throw new \InvalidArgumentException("Variable with name `{$command->variable->name} already defined.`");
        }

        $envSpecification->set($command->variable);
        $specification->add($envSpecification);

        $this->envFileWriter->add($command->variable->name, $command->value);

        $this->specificationWriter->write($command->specificationFilePath, $specification);
    }
}
