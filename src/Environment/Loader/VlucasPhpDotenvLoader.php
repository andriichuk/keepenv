<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Environment\Loader;

use Dotenv\Dotenv;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class VlucasPhpDotenvLoader implements EnvFileLoaderInterface
{
    public function load(array $paths): array
    {
        $dotenv = Dotenv::createArrayBacked($paths);

        return $dotenv->load();
    }
}
