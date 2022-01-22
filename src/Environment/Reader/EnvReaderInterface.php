<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Environment\Reader;

interface EnvReaderInterface
{
    public function read(string ...$paths): array;
}
