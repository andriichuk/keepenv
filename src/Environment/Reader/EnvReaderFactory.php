<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Environment\Reader;

use RuntimeException;

class EnvReaderFactory
{
    public function baseOnAvailability(): EnvReaderInterface
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
