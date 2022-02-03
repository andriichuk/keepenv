<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Generator\Exceptions;

use RuntimeException;

class SpecGeneratorException extends RuntimeException
{
    public static function alreadyExists(): self
    {
        return new self('Specification file already exists and was not modified.');
    }
}
