<?php

declare(strict_types=1);

namespace Andriichuk\Enviro\Reader;

use Andriichuk\Enviro\Specification\Specification;
use Andriichuk\Enviro\Specification\SpecificationBuilderInterface;
use Symfony\Component\Yaml\Yaml;

class SpecificationYamlReader implements SpecificationReaderInterface
{
    private SpecificationBuilderInterface $builder;

    public function __construct(SpecificationBuilderInterface $builder)
    {
        $this->builder = $builder;
    }

    public function read(string $source, string $environment): Specification
    {
        if (!file_exists($source)) {
            throw new \InvalidArgumentException('Source file must exists.');
        }

        $specification = Yaml::parseFile($source);

        return $this->builder->build($environment, $specification);
    }
}
