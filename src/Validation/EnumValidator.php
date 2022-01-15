<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Validation;

use InvalidArgumentException;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class EnumValidator implements ValidatorInterface
{
    public function alias(): string
    {
        return 'enum';
    }

    public function message(array $placeholders): string
    {
        return sprintf(
            'The value must match one of the values: %s.',
            implode(', ', $placeholders['cases'] ?? []),
        );
    }

    /**
     * @inheritDoc
     */
    public function validate($value, array $options): bool
    {
        if (empty($options)) {
            throw new InvalidArgumentException('Cases for the `enum` validator cannot be empty. It should be an array of values.');
        }

        return in_array($value, $options);
    }
}
