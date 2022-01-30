<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Validation\Rules;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class NumericRule implements RuleInterface
{
    public function alias(): string
    {
        return 'numeric';
    }

    public function acceptsFalseOption(): bool
    {
        return false;
    }

    public function message(array $placeholders): string
    {
        return 'The value must be a numeric.';
    }

    /**
     * @inheritDoc
     */
    public function validate($value, $options): bool
    {
        return is_numeric($value);
    }
}
