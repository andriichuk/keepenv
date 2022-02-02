<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Validation\Rules;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class BooleanRule implements RuleInterface
{
    public function alias(): string
    {
        return 'boolean';
    }

    public function acceptsFalseOption(): bool
    {
        return false;
    }

    public function message(array $placeholders): string
    {
        return 'The value must be a boolean.';
    }

    /**
     * @inheritDoc
     */
    public function validate($value, $options): bool
    {
        if (is_array($options)) {
            $true = isset($options['true']) ? (string) $options['true'] : null;
            $false = isset($options['false']) ? (string) $options['false'] : null;

            if (!isset($true, $false) || $true === '' || $true === $false) {
                throw new \InvalidArgumentException('Invalid arguments');
            }

            return in_array($value, [$true, $false], true);
        }

        if (is_string($value) && trim($value) === '') {
            return false;
        }

        return filter_var($value, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE) !== null;
    }
}
