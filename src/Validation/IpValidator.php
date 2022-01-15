<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Validation;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class IpValidator implements ValidatorInterface
{
    public function alias(): string
    {
        return 'ip';
    }

    public function message(array $placeholders): string
    {
        return 'The value must be a valid IP address.';
    }

    /**
     * @inheritDoc
     */
    public function validate($value, array $options): bool
    {
        return filter_var($value, FILTER_VALIDATE_IP) !== false;
    }
}
