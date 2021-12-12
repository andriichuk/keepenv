<?php

declare(strict_types=1);

namespace Andriichuk\Enviro\Specification;

class SpecificationArrayBuilder implements SpecificationBuilderInterface
{
    public function build(array $rawDefinition): Specification
    {
        $environments = array_keys($rawDefinition);
        $common = $rawDefinition['common'] ?? [];

        $specification = new Specification();

        foreach ($environments as $environment) {
            if ($environment === 'common') {
                $variables = $common;
            } else {
                $variables = array_replace_recursive(
                    $common,
                    !empty($rawDefinition[$environment]) ? $rawDefinition[$environment] : [], // TODO: check that value is an array
                );
            }

            $envSpecification = new EnvSpecification($environment);

            foreach ($variables as $name => $definition) {
                $envSpecification->add(
                    new Variable(
                        $name,
                        $definition['description'] ?? '',
                        $definition['rules'] ?? null,
                        $definition['default'] ?? null,
                    ),
                );
            }

            $specification->add($envSpecification);
        }

        return $specification;
    }
}
