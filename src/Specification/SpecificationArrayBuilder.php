<?php

declare(strict_types=1);

namespace Andriichuk\Enviro\Specification;

class SpecificationArrayBuilder implements SpecificationBuilderInterface
{
    public function build(string $environment, array $variablesRaw): Specification
    {
        $common = $variablesRaw['common'] ?? [];
        $environmentSpecification = $variablesRaw[$environment] ?? null;

        if ($environmentSpecification === null) {
            throw new \InvalidArgumentException("Environment with the name `{$environment}` is not exists.");
        }

        $variables = array_replace_recursive(
            $common,
            $environmentSpecification,
        );

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
