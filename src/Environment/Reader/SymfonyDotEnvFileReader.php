<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Environment\Reader;

use Symfony\Component\Dotenv\Dotenv;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class SymfonyDotEnvFileReader implements EnvReaderInterface
{
    private Dotenv $dotenv;

    public function __construct()
    {
        $this->dotenv = new Dotenv();
    }

    public function read(string ...$paths): array
    {
        $variables = [];

        foreach ($paths as $path) {
            if (!is_readable($path) || is_dir($path)) {
                throw new \RuntimeException('Unable to read env file ' . $path);
            }

            $variables = array_merge(
                $variables,
                $this->dotenv->parse((string) file_get_contents($path), $path),
            );
        }

        return $variables;
    }
}
