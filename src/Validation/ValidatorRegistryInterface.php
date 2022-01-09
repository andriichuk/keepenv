<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Validation;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
interface ValidatorRegistryInterface
{
    public function add(ValidatorInterface $validator): void;

    public function get(string $alias): ValidatorInterface;
}
