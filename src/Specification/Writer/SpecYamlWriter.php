<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Specification\Writer;

use Andriichuk\KeepEnv\Specification\Specification;
use Andriichuk\KeepEnv\Specification\Writer\SpecWriterInterface;
use Symfony\Component\Yaml\Yaml;

class SpecYamlWriter implements SpecWriterInterface
{
    public function write(string $filePath, Specification $specification): void
    {
        $yaml = Yaml::dump($specification->toArray(), 5, 4, Yaml::DUMP_EXCEPTION_ON_INVALID_TYPE);

        file_put_contents($filePath, $yaml);
    }
}
