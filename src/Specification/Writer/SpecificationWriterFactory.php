<?php

declare(strict_types=1);

namespace Andriichuk\Enviro\Specification\Writer;

class SpecificationWriterFactory
{
    public function basedOnResource(string $sourcePath): SpecificationWriterInterface
    {
        $type = pathinfo($sourcePath, PATHINFO_EXTENSION);

        switch ($type) {
            case 'php':
                throw new \InvalidArgumentException('Not implemented yet.');

            case 'yml':
            case 'yaml':
                return new SpecificationYamlWriter();

            default:
                throw new \InvalidArgumentException("Unsupported writer type `{$type}`.");
        }
    }
}
