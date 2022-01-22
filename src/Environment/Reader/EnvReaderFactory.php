<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Environment\Reader;

use RuntimeException;

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

            default:
                throw new RuntimeException('DotEnv library not found.');
        }
    }

    private function baseOnAvailability(): EnvReaderInterface
    {
        switch (true) {
            case class_exists(\Dotenv\Dotenv::class):
                return new VlucasPhpDotEnvFileReader();

            case class_exists(\Symfony\Component\Dotenv\Dotenv::class):
                return new SymfonyDotEnvFileReader();

            default:
                throw new RuntimeException('DotEnv library not found.');
        }
    }
}
