<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Validation\Rules;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class IpRule implements RuleInterface
{
    public function alias(): string
    {
        return 'ip';
    }

    public function acceptsFalseOption(): bool
    {
        return false;
    }

    public function message(array $placeholders): string
    {
        return 'The value must be a valid IP address.';
    }

    /**
     * @inheritDoc
     */
    public function validate($value, $options): bool
    {
        return filter_var($value, FILTER_VALIDATE_IP) !== false;
    }
}
