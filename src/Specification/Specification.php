<?php

declare(strict_types=1);

namespace Andriichuk\Enviro\Specification;

class Specification
{
    private string $environmentName;
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
}
