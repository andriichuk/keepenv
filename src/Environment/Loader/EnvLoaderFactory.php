<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Environment\Loader;

use RuntimeException;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class EnvLoaderFactory
{
    public function make(string $loader): EnvLoaderInterface
    {
        switch ($loader) {
            case 'system':
                return new SystemEnvStateLoader();

            case 'auto':
            default:
                return $this->baseOnAvailability();
        }
    }

    public function baseOnAvailability(): EnvLoaderInterface
    {
        switch (true) {
            case class_exists(\Dotenv\Dotenv::class):
                return new VlucasPhpDotEnvStateLoader();

            case class_exists(\Symfony\Component\Dotenv\Dotenv::class):
                return new SymfonyDotEnvStateLoader();

            default:
                throw new RuntimeException('DotEnv library not found.');
        }
    }
}
