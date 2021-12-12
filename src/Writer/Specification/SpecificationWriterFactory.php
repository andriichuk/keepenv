<?php

declare(strict_types=1);

namespace Andriichuk\Enviro\Writer\Specification;

use Andriichuk\Enviro\Specification\SpecificationArrayBuilder;

class SpecificationWriterFactory
{
    public function basedOnFileExtension(string $sourcePath): SpecificationWriterInterface
    {
        $parts = explode('.', $sourcePath);
        $type = (string) end($parts);

        switch ($type) {
            case 'php':
                throw new \InvalidArgumentException('Not implemented yet.');

            case 'yaml':
                return new SpecificationYamlWriter();

            default:
                throw new \InvalidArgumentException("Unsupported type `{$type}`");
        }
    }
}
