<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Specification\Reader;

use Andriichuk\KeepEnv\Specification\SpecificationArrayBuilder;
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
