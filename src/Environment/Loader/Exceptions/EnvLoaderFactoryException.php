<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Environment\Loader\Exceptions;

use RuntimeException;

class EnvLoaderFactoryException extends RuntimeException
{
    public static function undefined(string $type): self
    {
        return new self("DotEnv loader type `$type` not found.");
    }

    public static function notFound(): self
    {
        return new self('DotEnv library not found.');
    }
}
