<?php

declare(strict_types=1);

namespace Andriichuk\Enviro\Contracts;

interface ArraySerializable
{
    public function toArray(): array;
}
