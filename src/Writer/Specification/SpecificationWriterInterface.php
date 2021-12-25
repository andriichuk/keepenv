<?php

declare(strict_types=1);

namespace Andriichuk\Enviro\Writer\Specification;

use Andriichuk\Enviro\Specification\Specification;

interface SpecificationWriterInterface
{
    public function write(string $filePath, Specification $specification): void;
}
