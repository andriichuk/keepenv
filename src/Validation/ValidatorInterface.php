<?php

declare(strict_types=1);

namespace Andriichuk\EnvValidator\Validation;

interface ValidatorInterface
{
    public function alias(): string;

    public function message(): string;

    public function validate($value, array $options): bool;
}
