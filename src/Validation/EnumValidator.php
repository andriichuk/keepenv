<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Validation;

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
        return in_array($value, $options); // TODO: check options type and emptiness
    }
}
