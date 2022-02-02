<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Environment\Loader;

use josegonzalez\Dotenv\Loader;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class JoseGonzalezDotEnvStateLoader implements EnvLoaderInterface
{
    public function load(array $paths, bool $overrideExisting): array
    {
        $loader = new Loader($paths);
        $loader->parse();

        if (!$overrideExisting) {
            $loader->skipExisting();
        }

        $loader->toEnv($overrideExisting);

        return $_ENV;
    }
}
