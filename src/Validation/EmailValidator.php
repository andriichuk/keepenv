<?php

declare(strict_types=1);

namespace Andriichuk\EnvValidator\Validation;

class EmailValidator implements ValidatorInterface
{
    public function alias(): string
    {
        return 'email';
    }

    public function message(): string
    {
        return 'The must an email.';
    }

    public function validate($value, array $options): bool
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL);
    }
}
