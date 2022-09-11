<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Environment\Reader;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class SystemEnvReader implements EnvReaderInterface
{
    public function read(string ...$paths): array
    {
        return $_ENV;
    }
}
