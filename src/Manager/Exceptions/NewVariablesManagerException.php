<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Manager\Exceptions;

use RuntimeException;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class NewVariablesManagerException extends RuntimeException
{
    public static function variableAlreadyDefined(string $name): self
    {
        return new self("Variable with name `{$name}` already defined.");
    }
}
