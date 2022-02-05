<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Specification\Writer\Exceptions;

use RuntimeException;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class SpecWriterException extends RuntimeException
{
    public static function cannotWrite(string $path): self
    {
        return new self("Failed to write YAML content to the file [$path].");
    }
}
