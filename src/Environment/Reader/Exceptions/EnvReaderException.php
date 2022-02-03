<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Environment\Reader\Exceptions;

use RuntimeException;

class EnvReaderException extends RuntimeException
{
    public static function notReadable(string $path): self
    {
        return new self("Unable to read dotenv file $path.");
    }
}
