<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Validation\Rules;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class EqualsRule implements RuleInterface
{
    public function alias(): string
    {
        return 'equals';
    }

    public function acceptsFalseOption(): bool
    {
        return true;
    }

    public function message(array $placeholders): string
    {
        return "The value must be equal to `{$placeholders['equals']}`.";
    }

    /**
     * @inheritDoc
     */
    public function validate($value, $options): bool
    {
        return $value === $options;
    }
}
