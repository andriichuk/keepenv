<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Specification\Writer;

use Andriichuk\KeepEnv\Specification\Specification;
use Andriichuk\KeepEnv\Specification\Writer\Exceptions\SpecWriterException;
use Symfony\Component\Yaml\Yaml;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class SpecYamlWriter implements SpecWriterInterface
{
    public function write(string $filePath, Specification $specification): void
    {
        $yaml = Yaml::dump($specification->toArray(), 15, 4, Yaml::DUMP_EXCEPTION_ON_INVALID_TYPE);
        $successfullyWritten = file_put_contents($filePath, $yaml);

        if ($successfullyWritten === false) {
            throw SpecWriterException::cannotWrite($filePath);
        }
    }
}
