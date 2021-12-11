<?php

declare(strict_types=1);

namespace Andriichuk\Enviro\State;

use Andriichuk\Enviro\State\EnvStateProviderInterface;

class EnvStateProvider implements EnvStateProviderInterface
{
    public function get(string $variable)
    {
        return $_ENV[$variable] ?? null;
    }
}
