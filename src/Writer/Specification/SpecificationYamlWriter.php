<?php

declare(strict_types=1);

namespace Andriichuk\Enviro\Writer\Specification;

use Andriichuk\Enviro\Specification\Specification;
use Symfony\Component\Yaml\Yaml;

class SpecificationYamlWriter implements SpecificationWriterInterface
{
    public function write(string $filePath, Specification $specification): void
    {
        $yaml = Yaml::dump($specification->toArray(), 5, 4, Yaml::DUMP_EXCEPTION_ON_INVALID_TYPE);

        file_put_contents(
            dirname(__DIR__, 3) . '/stubs/env-new.spec.yaml',
            $yaml,
        );
    }
}
