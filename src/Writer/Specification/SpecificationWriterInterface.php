<?php

declare(strict_types=1);

namespace Andriichuk\Enviro\Writer\Specification;

interface SpecificationWriterInterface
{
    public function write(string $filePath, string $environment): void;
}
