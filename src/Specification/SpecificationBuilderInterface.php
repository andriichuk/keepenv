<?php

declare(strict_types=1);

namespace Andriichuk\Enviro\Specification;

interface SpecificationBuilderInterface
{
    public function build(string $environment, array $variables): Specification;
}
