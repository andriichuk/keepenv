<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Specification;

interface SpecificationBuilderInterface
{
    /**
     * @psalm-param array{version: string, environments: array} $rawDefinition
     */
    public function build(array $rawDefinition): Specification;
}
