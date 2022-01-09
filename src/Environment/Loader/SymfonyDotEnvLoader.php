<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Environment\Loader;

use Symfony\Component\Dotenv\Dotenv;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class SymfonyDotEnvLoader implements EnvFileLoaderInterface
{
    private Dotenv $dotenv;

    public function __construct()
    {
        $this->dotenv = new Dotenv();
    }

    public function load(array $paths): array
    {
        $variables = [];

        foreach ($paths as $path) {
            if (!is_readable($path) || is_dir($path)) {
                throw new \RuntimeException('Unable to read env file ' . $path);
            }

            $variables = array_merge(
                $variables,
                $this->dotenv->parse(file_get_contents($path), $path),
            );
        }

        return $variables;
    }
}
