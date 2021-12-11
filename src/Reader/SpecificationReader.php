<?php

declare(strict_types=1);

namespace Andriichuk\Enviro\Reader;

use Andriichuk\Enviro\Reader\SpecificationReaderInterface;

class SpecificationReader implements SpecificationReaderInterface
{
    private array $specification;

    public function __construct(array $specification)
    {
        $this->specification = $specification;
    }

    public function read(): array
    {
        return $this->specification;
    }
}
