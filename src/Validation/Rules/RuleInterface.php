<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Validation\Rules;

use Andriichuk\KeepEnv\Validation\Rules\Exceptions\RuleOptionsException;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
interface RuleInterface
{
    /**
     * Validator alias as unique identifier for validator in validation registry.
     */
    public function alias(): string;

    /**
     * Determines if the rule accepts false options to skip validations like `email: false`, `string: false` etc.
     */
    public function acceptsFalseOption(): bool;

    /**
     * Validation error message.
     */
    public function message(array $placeholders): string;

    /**
     * @param mixed $value
     * @param mixed $options
     *
     * @throws RuleOptionsException
     */
    public function validate($value, $options): bool;
}
