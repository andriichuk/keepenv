<?php

declare(strict_types=1);

namespace Andriichuk\Enviro\Validation;

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

    /**
     * @inheritDoc
     */
    public function validate($value, array $options): bool
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }
}
