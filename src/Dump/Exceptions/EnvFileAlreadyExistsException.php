<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Dump\Exceptions;

use RuntimeException;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class EnvFileAlreadyExistsException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Environment file already exists and was not modified.');
    }
}
