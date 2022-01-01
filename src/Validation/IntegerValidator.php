<?php

declare(strict_types=1);

namespace Andriichuk\Enviro\Validation;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class IntegerValidator implements ValidatorInterface
{
    public function alias(): string
    {
        return 'int';
    }

    public function message(array $placeholders): string
    {
        return 'The value must be an integer.';
    }

    /**
     * @inheritDoc
     */
    public function validate($value, array $options): bool
    {
        return filter_var($value, FILTER_VALIDATE_INT) !== false;
    }
}
