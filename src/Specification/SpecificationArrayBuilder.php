<?php

declare(strict_types=1);

namespace Andriichuk\Enviro\Specification;

class SpecificationArrayBuilder implements SpecificationBuilderInterface
{
    public function build(array $rawDefinition): Specification
    {
        $version = $rawDefinition['version'] ?? null;

        if ($version === null) {
            throw new \InvalidArgumentException('Missing version key.');
        }

        $environments = array_keys($rawDefinition['environments']);
        $common = $rawDefinition['common'] ?? [];

        $specification = new Specification($version);

        foreach ($environments as $environment) {
            if ($environment === 'common') {
                $variables = $common;
            } else {
                $variables = array_replace_recursive(
                    $common,
                    !empty($rawDefinition[$environment]) ? $rawDefinition[$environment] : [], // TODO: check that value is an array
                );
            }

            $envSpecification = new EnvVariables($environment);

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
