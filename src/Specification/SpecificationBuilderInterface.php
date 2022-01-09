<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Specification;

interface SpecificationBuilderInterface
{
    public function build(array $rawDefinition): Specification;
}
