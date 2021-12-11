<?php

declare(strict_types=1);

namespace Andriichuk\Enviro\Specification;

use Andriichuk\Enviro\Reader\SpecificationReaderInterface;

class SpecificationLoader
{
    private SpecificationReaderInterface $reader;

    public function __construct(SpecificationReaderInterface $reader)
    {
        $this->reader = $reader;
    }

    public function load(string $environment): Specification
    {
        $variables = $this->reader->read($environment);
        $specification = new Specification($environment);

        foreach ($variables as $name => $definition) {
            $specification->add(
                new Variable(
                    $name,
                    $definition['description'] ?? '',
                    $definition['default'] ?? '',
                    $definition['rules'] ?? [],
                ),
            );
        }

        return $specification;
    }
}
