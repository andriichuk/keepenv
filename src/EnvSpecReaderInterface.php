<?php

declare(strict_types=1);

namespace Andriichuk\EnvValidator;

interface EnvSpecReaderInterface
{
    public function read(): array;
}
