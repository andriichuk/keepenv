<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Validation\Rules\Exceptions;

use Exception;

class RuleOptionsException extends Exception
{
    public static function invalidRequiredOption(): self
    {
        return new self('Invalid type of `required` rule option. It should be a boolean.');
    }

    public static function invalidCasesForEnum(): self
    {
        return new self('Cases for the `enum` validator cannot be empty. It should be an array of values.');
    }

    public static function invalidStringOptions(): self
    {
        return new self('Invalid option value for string rule. It must be a `true` or min/max ranges.');
    }

    public static function invalidStringRanges(): self
    {
        return new self('Min length cannot be greater than max.');
    }
}
