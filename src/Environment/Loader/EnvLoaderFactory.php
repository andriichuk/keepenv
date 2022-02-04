<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Environment\Loader;

use Andriichuk\KeepEnv\Environment\Loader\Exceptions\EnvLoaderFactoryException;

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

            case 'vlucas/phpdotenv':
                return new VlucasPhpDotEnvStateLoader();

            case 'symfony/dotenv':
                return new SymfonyDotEnvStateLoader();

            case 'josegonzalez/dotenv':
                return new JoseGonzalezDotEnvStateLoader();

            default:
                throw EnvLoaderFactoryException::undefined($type);
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
                throw EnvLoaderFactoryException::notFound();
        }
    }
}
