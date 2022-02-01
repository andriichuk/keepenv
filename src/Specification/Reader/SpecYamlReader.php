<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Specification\Reader;

use Andriichuk\KeepEnv\Specification\Specification;
use Andriichuk\KeepEnv\Specification\SpecificationBuilderInterface;
use InvalidArgumentException;
use RuntimeException;
use Symfony\Component\Yaml\Yaml;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class SpecYamlReader implements SpecReaderInterface
{
    private SpecificationBuilderInterface $builder;

    public function __construct(SpecificationBuilderInterface $builder)
    {
        $this->builder = $builder;
    }

    public function read(string $source): Specification
    {
        if (!file_exists($source)) {
            throw new InvalidArgumentException('Source file must exists.');
        }

        $spec = Yaml::parseFile($source);

        if (!is_array($spec)) {
            throw new RuntimeException('Unable to parse Yaml specification.');
        }

        return $this->builder->build($spec);
    }
}
