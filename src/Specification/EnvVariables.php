<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Specification;

use Andriichuk\KeepEnv\Contracts\ArraySerializable;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class EnvVariables implements ArraySerializable
{
    private string $envName;
    private ?string $extends;

    /**
     * @var Variable[]
     */
    private array $variables = [];

    public function __construct(string $envName, ?string $extends = null)
    {
        $this->envName = $envName;
        $this->extends = $extends;
    }

    public function getEnvName(): string
    {
        return $this->envName;
    }

    public function getExtends(): ?string
    {
        return $this->extends;
    }

    public function add(Variable $variable): void
    {
        $this->variables[$variable->name] = $variable;
    }

    public function get(string $variable): ?Variable
    {
        return $this->variables[$variable] ?? null;
    }

    public function remove(string $variable): void
    {
        unset($this->variables[$variable]);
    }

    /**
     * @return Variable[]
     */
    public function all(): array
    {
        return $this->variables;
    }

    public function count(): int
    {
        return count($this->variables);
    }

    /**
     * @param string[] $keys
     *
     * @return Variable[]
     */
    public function onlyWithKeys(array $keys): array
    {
        return array_filter(
            $this->variables,
            static fn (Variable $variable): bool => in_array($variable->name, $keys, true),
        );
    }

    public function toArray(): array
    {
        $plainArrayVariables = [];

        foreach ($this->variables as $variable) {
            $plainArrayVariables[$variable->name] = $variable->toArray();
        }

        return array_filter([
            'extends' => $this->extends,
            'variables' => $plainArrayVariables,
        ]);
    }
}
