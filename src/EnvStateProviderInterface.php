<?php

declare(strict_types=1);

namespace Andriichuk\EnvValidator;

interface EnvStateProviderInterface
{
    public function get(string $variable);
}
