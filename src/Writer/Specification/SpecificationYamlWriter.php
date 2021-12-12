<?php

declare(strict_types=1);

namespace Andriichuk\Enviro\Writer\Specification;

use Andriichuk\Enviro\Reader\SpecificationYamlReader;
use Andriichuk\Enviro\Specification\SpecificationArrayBuilder;
use Symfony\Component\Yaml\Yaml;

class SpecificationYamlWriter implements SpecificationWriterInterface
{
    public function write(string $filePath, string $environment): void
    {
        $reader = new SpecificationYamlReader(new SpecificationArrayBuilder());

        $specification = $reader->read($filePath);
        $yaml = Yaml::dump($specification->toArray(), 5, 4, Yaml::DUMP_EXCEPTION_ON_INVALID_TYPE);

        file_put_contents(
            dirname(__DIR__, 3) . '/stubs/env-new.spec.yaml',
            $yaml,
        );
    }
}
