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
        return sprintf(
            'The `%s` variable must be equal to `%s`. Given `%s`',
            $placeholders['name'] ?? '',
            $placeholders['equals'] ?? '',
            $placeholders['value'] ?? '',
        );
    }

    /**
     * @inheritDoc
     */
    public function validate($value, array $options): bool
    {
        return $value === reset($options); // TODO: check false
    }
}
