<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Specification\Reader\Exceptions;

use RuntimeException;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class SpecReaderException extends RuntimeException
{
    public static function unsupportedType(string $sourcePath, string $type): self
    {
        return new self("Unsupported specification source type `$type`, file `$sourcePath`");
    }
}
