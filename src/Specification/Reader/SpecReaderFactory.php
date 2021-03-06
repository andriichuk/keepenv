<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Specification\Reader;

use Andriichuk\KeepEnv\Specification\Reader\Exceptions\SpecReaderException;
use Andriichuk\KeepEnv\Specification\SpecificationArrayBuilder;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class SpecReaderFactory
{
    public function basedOnSource(string $sourcePath): SpecReaderInterface
    {
        $type = pathinfo($sourcePath, PATHINFO_EXTENSION);

        switch ($type) {
            case 'yml':
            case 'yaml':
                return new SpecYamlReader(new SpecificationArrayBuilder());

            default:
                throw SpecReaderException::unsupportedType($sourcePath, $type);
        }
    }
}
