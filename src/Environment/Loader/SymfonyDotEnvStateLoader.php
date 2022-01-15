<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Environment\Loader;

use Symfony\Component\Dotenv\Dotenv;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class SymfonyDotEnvStateLoader implements EnvLoaderInterface
{
    private Dotenv $dotenv;

    public function __construct()
    {
        $this->dotenv = new Dotenv();
    }

    public function load(array $paths, bool $overrideExisting): array
    {
        if ($overrideExisting) {
            $this->dotenv->overload(...$paths);
        } else {
            $this->dotenv->load(...$paths);
        }

        return $_ENV;
    }
}
