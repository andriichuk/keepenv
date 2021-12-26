<?php

declare(strict_types=1);

namespace Andriichuk\Enviro\Validation;

class IntegerValidator implements ValidatorInterface
{
    public function alias(): string
    {
        return 'int';
    }

    public function message(): string
    {
        return 'The must an integer.';
    }

    public function validate($value, array $options): bool
    {
        return filter_var($value, FILTER_VALIDATE_INT) !== false;
    }
}
