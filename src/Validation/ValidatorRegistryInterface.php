<?php

declare(strict_types=1);

namespace Andriichuk\Enviro\Validation;

interface ValidatorRegistryInterface
{
    public function add(ValidatorInterface $validator): void;

    public function get(string $alias): ValidatorInterface;
}
