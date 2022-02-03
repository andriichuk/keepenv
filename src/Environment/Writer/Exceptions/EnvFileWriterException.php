<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Environment\Writer\Exceptions;

use RuntimeException;

class EnvFileWriterException extends RuntimeException
{
    public static function keyAlreadyDefined(string $key): self
    {
        return new self("$key is already defined.");
    }
}
