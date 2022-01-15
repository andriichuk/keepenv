<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Environment\Loader;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class SystemEnvStateLoader implements EnvLoaderInterface
{
    public function load(array $paths, bool $overrideExisting): array
    {
        return $_ENV;
    }
}
