<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Specification\Writer;

use Andriichuk\KeepEnv\Specification\Writer\Exceptions\SpecFactoryException;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class SpecWriterFactory
{
    public function basedOnResource(string $sourcePath): SpecWriterInterface
    {
        $type = pathinfo($sourcePath, PATHINFO_EXTENSION);

        switch ($type) {
            case 'yml':
            case 'yaml':
                return new SpecYamlWriter();

            case 'xml':
            case 'json':
            case 'php':
                throw SpecFactoryException::notImplementedYet($type);

            default:
                throw SpecFactoryException::notSupported($type);
        }
    }
}
