<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Validation\Rules;

use Andriichuk\KeepEnv\Validation\Rules\Exceptions\RuleOptionsException;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class RequiredRule implements RuleInterface
{
    public function alias(): string
    {
        return 'required';
    }

    public function acceptsFalseOption(): bool
    {
        return true;
    }

    public function message(array $placeholders): string
    {
        return 'The value is required.';
    }

    /**
     * @inheritDoc
     */
    public function validate($value, $options): bool
    {
        if (!is_bool($options)) {
            throw RuleOptionsException::invalidRequiredOption();
        }

        if (!$options) {
            return true;
        }

        if ($value === null) {
            return false;
        }

        return is_string($value) && trim($value) !== '';
    }
}
