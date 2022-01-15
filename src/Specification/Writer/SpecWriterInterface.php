<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Specification\Writer;

use Andriichuk\KeepEnv\Specification\Specification;

interface SpecWriterInterface
{
    public function write(string $filePath, Specification $specification): void;
}
