<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Validation;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
interface ValidatorInterface
{
    public function alias(): string;

    public function message(array $placeholders): string;

    /**
     * @param mixed $value
     */
    public function validate($value, array $options): bool;
}
