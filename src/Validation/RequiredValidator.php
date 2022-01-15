<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Validation;

use InvalidArgumentException;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class RequiredValidator implements ValidatorInterface
{
    public function alias(): string
    {
        return 'required';
    }

    public function message(array $placeholders): string
    {
        return 'The value is required.';
    }

    /**
     * @inheritDoc
     */
    public function validate($value, array $options): bool
    {
        $isRequired = $this->resolveOption($options);

        if (!$isRequired) {
            return true;
        }

        if ($value === null) {
            return false;
        }

        return is_string($value) && trim($value) !== '';
    }

    private function resolveOption(array $options): bool
    {
        $option = reset($options);

        if (is_string($option)) {
            if (!in_array($option, ['true', 'false'], true)) {
                throw new InvalidArgumentException("Invalid `required` rule option: $option. Available options: true, false, 'true', 'false'.");
            }

            return [
                'true' => true,
                'false' => false,
            ][$option];
        }

        if (!is_bool($option)) {
            throw new InvalidArgumentException("Invalid type of `required` rule option. It should be a string or boolean.");
        }

        return $option;
    }
}
