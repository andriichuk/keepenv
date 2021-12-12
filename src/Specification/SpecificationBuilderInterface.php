<?php

declare(strict_types=1);

namespace Andriichuk\Enviro\Specification;

interface SpecificationBuilderInterface
{
    public function build(array $rawDefinition): Specification;
}
