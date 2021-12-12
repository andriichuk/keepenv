<?php

declare(strict_types=1);

namespace Andriichuk\Enviro\Specification;

use Andriichuk\Enviro\Contracts\ArraySerializable;

class EnvSpecification implements ArraySerializable
{
    private string $environmentName;

    /**
     * @var array<Variable>
     */
    private array $variables = [];

    public function __construct(string $environmentName)
    {
        $this->environmentName = $environmentName;
    }

    public function getEnvironmentName(): string
    {
        return $this->environmentName;
    }

    public function add(Variable $variable): void
    {
        $this->variables[$variable->name] = $variable;
    }

    public function get(string $variable)
    {
        return $this->variables[$variable] ?? null;
    }

    public function set(Variable $variable): void
    {
        $this->variables[$variable->name] = $variable;
    }

    /**
     * @var array<Variable>
     */
    public function all(): array
    {
        return $this->variables;
    }

    public function toArray(): array
    {
        $plainArrayVariables = [];

        foreach ($this->variables as $variable) {
            $plainArrayVariables[$variable->name] = $variable->toArray();
        }

        return $plainArrayVariables;
    }
}
