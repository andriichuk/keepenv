<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Environment\Reader;

use josegonzalez\Dotenv\Loader;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class JoseGonzalezDotEnvFileReader implements EnvReaderInterface
{
    public function read(string ...$paths): array
    {
        $loader = new Loader($paths);
        $loader->parse();

        return $loader->toArray();
    }
}
