<?php

declare(strict_types=1);

namespace Andriichuk\Enviro\Manager;

use Andriichuk\Enviro\Specification\Variable;

class AddVariableCommand
{
    public Variable $variable;
    public string $value;
    public string $environment;
    public string $envFilePath;
    public string $specificationFilePath;

    public function __construct(Variable $variable, string $value, string $environment, string $envFilePath, string $specificationFilePath)
    {
        $this->variable = $variable;
        $this->value = $value;
        $this->environment = $environment;
        $this->envFilePath = $envFilePath;
        $this->specificationFilePath = $specificationFilePath;
    }
}
