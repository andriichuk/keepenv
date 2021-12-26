<?php

declare(strict_types=1);

namespace Andriichuk\Enviro\Validation;

interface ValidatorInterface
{
    public function alias(): string;

    public function message(): string;

    /**
     * @param mixed $value
     */
    public function validate($value, array $options): bool;
}
