<?php

declare(strict_types=1);

namespace Andriichuk\EnvValidator;

class EnvState
{
    private array $variables = [];

    public function add(EnvVariable $variable): void
    {
        $this->variables[$variable->name] = $variable;
    }

    public function get(string $variable)
    {
        return $this->variables[$variable] ?? null;
    }
}
