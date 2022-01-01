<?php

declare(strict_types=1);

namespace Andriichuk\Enviro\Validation;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class EqualsValidator implements ValidatorInterface
{
    public function alias(): string
    {
        return 'equals';
    }

    public function message(array $placeholders): string
    {
        return "The value must be equal to `{$placeholders['equals']}`.";
    }

    /**
     * @inheritDoc
     */
    public function validate($value, array $options): bool
    {
        return $value === reset($options); // TODO: check false
    }
}
