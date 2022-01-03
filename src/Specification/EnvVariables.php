<?php

declare(strict_types=1);

namespace Andriichuk\Enviro\Specification;

use Andriichuk\Enviro\Contracts\ArraySerializable;

class EnvVariables implements ArraySerializable
{
    private string $envName;

    /**
     * @var Variable[]
     */
    private array $variables = [];

    public function __construct(string $envName)
    {
        $this->envName = $envName;
    }

    public function getEnvName(): string
    {
        return $this->envName;
    }

    public function add(Variable $variable): void
    {
        $this->variables[$variable->name] = $variable;
    }

    public function get(string $variable): ?Variable
    {
        return $this->variables[$variable] ?? null;
    }

    public function has(string $variable): bool
    {
        return isset($this->variables[$variable]);
    }

    public function set(Variable $variable): void
    {
        $this->variables[$variable->name] = $variable;
    }

    public function remove(string $variable): void
    {
        unset($this->variables[$variable]);
    }

    public function exceptKeys()
    {

    }

    /**
     * @return Variable[]
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
