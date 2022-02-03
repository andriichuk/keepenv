<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Environment\Reader\Exceptions;

use RuntimeException;

class EnvReaderFactoryException extends RuntimeException
{
    public static function notFound(): self
    {
        return new self('DotEnv reader library not found.');
    }
}
