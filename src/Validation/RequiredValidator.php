<?php

declare(strict_types=1);

namespace Andriichuk\Enviro\Validation;

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
        $isEmpty = empty($value);
        $isRequired = (bool) reset($options); // TODO: check type

        if (!$isRequired) {
            return true;
        }

        return !$isEmpty;
    }
}
