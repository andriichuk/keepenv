<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Environment\Provider;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
interface EnvStateProviderInterface
{
    public function get(string $variableName): ?string;
}
