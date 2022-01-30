<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Specification\Writer\Exceptions;

use OutOfBoundsException;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class SpecFactoryException extends OutOfBoundsException
{
    public static function notImplementedYet(string $type): self
    {
        return new self("Specification file type [$type] is not implemented yet.");
    }

    public static function notSupported(string $type): self
    {
        return new self("Specification file type [$type] is not supported.");
    }
}
