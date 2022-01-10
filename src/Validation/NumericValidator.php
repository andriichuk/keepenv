<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Validation;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class NumericValidator implements ValidatorInterface
{
    public function alias(): string
    {
        return 'numeric';
    }

    public function message(array $placeholders): string
    {
        return 'The value must be a numeric.';
    }

    /**
     * @inheritDoc
     */
    public function validate($value, array $options): bool
    {
        return is_numeric($value);
    }
}
