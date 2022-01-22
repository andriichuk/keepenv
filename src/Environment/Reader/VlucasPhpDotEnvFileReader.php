<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Environment\Reader;

use Dotenv\Dotenv;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class VlucasPhpDotEnvFileReader implements EnvReaderInterface
{
    public function read(string ...$paths): array
    {
        $dotenv = Dotenv::createArrayBacked($paths);

        return $dotenv->load();
    }
}
