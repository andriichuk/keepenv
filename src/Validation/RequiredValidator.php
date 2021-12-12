<?php

declare(strict_types=1);

namespace Andriichuk\Enviro\Validation;

class RequiredValidator implements ValidatorInterface
{
    public function alias(): string
    {
        return 'required';
    }

    public function message(): string
    {
        return 'The value cannot be empty.';
    }

    public function validate($value, array $options): bool
    {
        $isEmpty = empty($value);
        $isRequired = (bool) reset($options); // TODO: check type

        if (!$isRequired) {
            return true;
        }

        return !$isEmpty;
    }
}
