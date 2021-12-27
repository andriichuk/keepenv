<?php

declare(strict_types=1);

namespace Andriichuk\Enviro\Reader\Specification;

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

    public function read(string $source): Specification
    {
        if (!file_exists($source)) {
            throw new \InvalidArgumentException('Source file must exists.');
        }

        return $this->builder->build(Yaml::parseFile($source));
    }
}
