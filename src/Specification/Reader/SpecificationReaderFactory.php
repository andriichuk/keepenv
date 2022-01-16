<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Specification\Reader;

use Andriichuk\KeepEnv\Specification\Reader\Exceptions\SpecificationReaderException;
use Andriichuk\KeepEnv\Specification\SpecificationArrayBuilder;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class SpecificationReaderFactory
{
    public function basedOnSource(string $sourcePath): SpecificationReaderInterface
    {
        $type = pathinfo($sourcePath, PATHINFO_EXTENSION);

        switch ($type) {
            case 'yml':
            case 'yaml':
                return new SpecificationYamlReader(new SpecificationArrayBuilder());

            default:
                throw SpecificationReaderException::unsupportedType($sourcePath, $type);
        }
    }
}
