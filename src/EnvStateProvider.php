<?php

declare(strict_types=1);

namespace Andriichuk\EnvValidator;

class EnvStateProvider implements EnvStateProviderInterface
{
    public function get(string $variable)
    {
        return $_ENV[$variable] ?? null;
    }
}
