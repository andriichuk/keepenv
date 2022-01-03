<?php

declare(strict_types=1);

namespace Andriichuk\Enviro\Environment\Loader;

use RuntimeException;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class EnvFileLoaderFactory
{
    public function baseOnAvailability(): EnvFileLoaderInterface
    {
        switch (true) {
            case class_exists(\Dotenv\Dotenv::class):
                return new VlucasPhpDotenvLoader();

            case class_exists(\Symfony\Component\Dotenv\Dotenv::class):
                return new SymfonyDotEnvLoader();

            default:
                throw new RuntimeException('DotEnv library not found.');
        }
    }
}
