<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Dump\Exceptions;

use RuntimeException;

class EnvFileAlreadyExistsException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Environment file already exists and was not modified.');
    }
}
