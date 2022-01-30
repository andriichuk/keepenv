<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Environment\Writer\Exceptions;

use RuntimeException;

class EnvFileManagerException extends RuntimeException
{
    public static function fileNotExists(string $path): self
    {
        return new self("Environment file does not exists [$path].");
    }

    public static function cannotRead(string $path): self
    {
        return new self("Cannot read environment file [$path].");
    }

    public static function cannotWrite(string $path): self
    {
        return new self("Cannot write new content to the environment file [$path].");
    }

    public static function cannotCreateFile(string $path): self
    {
        return new self("Cannot create the environment file [$path].");
    }
}
