<?php

declare(strict_types=1);

namespace Andriichuk\Enviro\Manager;

use Andriichuk\Enviro\Specification\Variable;
use Andriichuk\Enviro\Writer\Env\EnvFileWriter;
use Andriichuk\Enviro\Writer\Specification\SpecificationWriterInterface;

class AddNewVariableManager
{
    private EnvFileWriter $envFileWriter;
    private SpecificationWriterInterface $specificationWriter;

    public function __construct(EnvFileWriter $envFileWriter, SpecificationWriterInterface $specificationWriter)
    {
        $this->envFileWriter = $envFileWriter;
        $this->specificationWriter = $specificationWriter;
    }

    public function add(Variable $variable, string $environment)
    {
        $this->specificationWriter->write($variable, $environment);
    }
}
