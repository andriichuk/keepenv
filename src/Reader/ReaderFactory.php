<?php

declare(strict_types=1);

namespace Andriichuk\Enviro\Reader;

use Andriichuk\Enviro\Specification\SpecificationArrayBuilder;

class ReaderFactory
{
    /**
     * TODO: based on file type
     */
    public function basedOnFileExtension(string $sourcePath): SpecificationReaderInterface
    {
        $parts = explode('.', $sourcePath);
        $type = (string) end($parts);

        switch ($type) {
            case 'php':
                return new SpecificationPhpArrayReader(new SpecificationArrayBuilder());

            case 'yaml':
                return new SpecificationYamlReader(new SpecificationArrayBuilder());

            default:
                throw new \InvalidArgumentException("Unsupported type `{$type}`");
        }
    }
}
