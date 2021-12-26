<?php

declare(strict_types=1);

namespace Andriichuk\Enviro\State;

class EnvStateProvider implements EnvStateProviderInterface
{
    public function get(string $variable): ?string
    {
        return $_ENV[$variable] ?? null;
    }
}
