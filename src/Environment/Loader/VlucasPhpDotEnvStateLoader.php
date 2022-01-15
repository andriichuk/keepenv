<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Environment\Loader;

use Dotenv\Dotenv;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class VlucasPhpDotEnvStateLoader implements EnvLoaderInterface
{
    public function load(array $paths, bool $overrideExisting): array
    {
        if ($overrideExisting) {
            $dotenv = Dotenv::createImmutable($paths);
        } else {
            $dotenv = Dotenv::createMutable($paths);
        }

        $dotenv->load();

        return $_ENV;
    }
}
