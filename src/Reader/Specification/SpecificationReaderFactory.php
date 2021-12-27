<?php

declare(strict_types=1);

namespace Andriichuk\Enviro\Reader\Specification;

use Andriichuk\Enviro\Specification\SpecificationArrayBuilder;
use OutOfBoundsException;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class SpecificationReaderFactory
{
    public function basedOnResource(string $sourcePath): SpecificationReaderInterface
    {
        $type = pathinfo($sourcePath, PATHINFO_EXTENSION);

        switch ($type) {
            case 'php':
                return new SpecificationPhpArrayReader(new SpecificationArrayBuilder());

            case 'yml':
            case 'yaml':
                return new SpecificationYamlReader(new SpecificationArrayBuilder());

            default:
                throw new OutOfBoundsException("Unsupported reader type `$type`.");
        }
    }
}
