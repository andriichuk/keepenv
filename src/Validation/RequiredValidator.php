<?php

declare(strict_types=1);

namespace Andriichuk\EnvValidator\Validation;

class RequiredValidator implements ValidatorInterface
{
    public function alias(): string
    {
        return 'required';
    }

    public function message(): string
    {
        return 'The value cannot be empty.';
    }

    public function validate($value, array $options): bool
    {
        return !empty($value);
    }
}
