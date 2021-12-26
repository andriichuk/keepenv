<?php

declare(strict_types=1);

namespace Andriichuk\Enviro\Validation;

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

    /**
     * @inheritDoc
     */
    public function validate($value, array $options): bool
    {
        return $value === reset($options); // TODO: check false
    }
}
