<?php

declare(strict_types=1);

namespace Andriichuk\EnvValidator\Validation;

class EqualsValidator implements ValidatorInterface
{
    public function alias(): string
    {
        return 'equals';
    }

    public function message(): string
    {
        return 'The must be the same.';
    }

    public function validate($value, array $options): bool
    {
        return $value === reset($options); // TODO: check false
    }
}
