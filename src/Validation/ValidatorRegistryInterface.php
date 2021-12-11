<?php

declare(strict_types=1);

namespace Andriichuk\EnvValidator\Validation;

interface ValidatorRegistryInterface
{
    public function add(ValidatorInterface $validator): void;

    public function get(string $alias): ValidatorInterface;
}
