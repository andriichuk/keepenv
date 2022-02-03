<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Validation\Exceptions;

use RuntimeException;

class ValidationReportException extends RuntimeException
{
    public static function variableIsNotValid(string $message): self
    {
        return new self($message);
    }
}
