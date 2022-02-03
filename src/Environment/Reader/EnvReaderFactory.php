<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Environment\Reader;

use Andriichuk\KeepEnv\Environment\Reader\Exceptions\EnvReaderFactoryException;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class EnvReaderFactory
{
    public function make(string $type): EnvReaderInterface
    {
        switch ($type) {
            case 'auto':
                return $this->baseOnAvailability();

            case 'vlucas/phpdotenv':
                return new VlucasPhpDotEnvFileReader();

            case 'symfony/dotenv':
                return new SymfonyDotEnvFileReader();

            case 'josegonzalez/dotenv':
                return new JoseGonzalezDotEnvFileReader();

            default:
                throw EnvReaderFactoryException::notFound();
        }
    }

    private function baseOnAvailability(): EnvReaderInterface
    {
        switch (true) {
            case class_exists(\Dotenv\Dotenv::class):
                return new VlucasPhpDotEnvFileReader();

            case class_exists(\Symfony\Component\Dotenv\Dotenv::class):
                return new SymfonyDotEnvFileReader();

            case class_exists(\josegonzalez\Dotenv\Loader::class):
                return new JoseGonzalezDotEnvFileReader();

            default:
                throw EnvReaderFactoryException::notFound();
        }
    }
}
