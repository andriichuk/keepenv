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
        $paths = array_map(
            /** @param mixed $path */
            static fn ($path): string => (string) $path,
            $paths,
        );

        if ($overrideExisting) {
            $dotenv = Dotenv::createMutable($paths);
        } else {
            $dotenv = Dotenv::createImmutable($paths);
        }

        $dotenv->load();

        return $_ENV;
    }
}
