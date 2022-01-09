<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Contracts;

interface ArraySerializable
{
    public function toArray(): array;
}
