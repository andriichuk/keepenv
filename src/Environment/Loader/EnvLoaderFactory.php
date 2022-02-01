<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Environment\Loader;

use RuntimeException;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class EnvLoaderFactory
{
    public function make(string $type): EnvLoaderInterface
    {
        switch ($type) {
            case 'system':
                return new SystemEnvStateLoader();

            case 'auto':
                return $this->baseOnAvailability();

            default:
                throw new RuntimeException("DotEnv loader type `$type` not found.");
        }
    }

    public function baseOnAvailability(): EnvLoaderInterface
    {
        switch (true) {
            case class_exists(\Dotenv\Dotenv::class):
                return new VlucasPhpDotEnvStateLoader();

            case class_exists(\Symfony\Component\Dotenv\Dotenv::class):
                return new SymfonyDotEnvStateLoader();

            case class_exists(\josegonzalez\Dotenv\Loader::class):
                return new JoseGonzalezDotEnvStateLoader();

            default:
                throw new RuntimeException('DotEnv library not found.');
        }
    }
}
