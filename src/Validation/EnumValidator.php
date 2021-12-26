<?php

declare(strict_types=1);

namespace Andriichuk\Enviro\Validation;

class EnumValidator implements ValidatorInterface
{
    public function alias(): string
    {
        return 'enum';
    }

    public function message(): string
    {
        return 'The must an enum.';
    }

    /**
     * @inheritDoc
     */
    public function validate($value, array $options): bool
    {
        return in_array($value, $options); // TODO: check options type and emptiness
    }
}
