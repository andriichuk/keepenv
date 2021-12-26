<?php

declare(strict_types=1);

namespace Andriichuk\Enviro\State;

interface EnvStateProviderInterface
{
    public function get(string $variable): ?string;
}
