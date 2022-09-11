<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Environment\Reader;

final class EnvReaderType
{
    public const AUTO = 'auto';
    public const SYSTEM = 'system';
    public const VLUCAS = 'vlucas/phpdotenv';
    public const SYMFONY = 'symfony/dotenv';
    public const JOSEGONZALEZ = 'josegonzalez/dotenv';
}
