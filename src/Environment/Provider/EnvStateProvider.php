<?php

declare(strict_types=1);

namespace Andriichuk\Enviro\Environment\Provider;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class EnvStateProvider implements EnvStateProviderInterface
{
    public function get(string $variableName): ?string
    {
        $value = $_ENV[$variableName] ?? null;

        return $value !== false ? $value: null;
    }
}
