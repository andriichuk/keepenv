<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Specification\Writer;

class SpecWriterFactory
{
    public function basedOnResource(string $sourcePath): SpecWriterInterface
    {
        $type = pathinfo($sourcePath, PATHINFO_EXTENSION);

        switch ($type) {
            case 'php':
                throw new \InvalidArgumentException('Not implemented yet.');

            case 'yml':
            case 'yaml':
                return new SpecYamlWriter();

            default:
                throw new \InvalidArgumentException("Unsupported writer type `{$type}`.");
        }
    }
}
