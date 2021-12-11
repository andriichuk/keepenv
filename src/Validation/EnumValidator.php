<?php

declare(strict_types=1);

namespace Andriichuk\EnvValidator\Validation;

class EnumValidator implements ValidatorInterface
{
    public function alias(): string
    {
        return 'enum';
    }

    public function message(): string
    {
        return 'The must an enum.';
    }

    public function validate($value, array $options): bool
    {
        $strict = $options['strict'] ?? true;

        if (!is_bool($strict)) {
            throw new \InvalidArgumentException('Strict option should be a boolean value.');
        }

        $cases = $options['cases'] ?? [];

        if (!is_array($cases)) {
            throw new \InvalidArgumentException('Strict option should be an array.');
        }

        return in_array($value, $cases, $strict);
    }
}
