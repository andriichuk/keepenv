<?php

declare(strict_types=1);

namespace Andriichuk\Enviro\Reader;

interface SpecificationReaderInterface
{
    public function read(string $environment): array;
}
