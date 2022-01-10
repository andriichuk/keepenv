<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Specification;

use OutOfBoundsException;

class SpecificationArrayBuilder implements SpecificationBuilderInterface
{
    public function build(array $rawDefinition): Specification
    {
        $version = $rawDefinition['version'] ?? null;

        if ($version === null) {
            throw new OutOfBoundsException('Missing `version` key.');
        }

        if (!isset($rawDefinition['environments'])) {
            throw new OutOfBoundsException('Missing `environments` key.');
        }

        $specification = new Specification($version);

        foreach ($rawDefinition['environments'] as $environment => $envDefinition) {
            $variables = $envDefinition['variables'] ?? null;

            if ($variables === null) {
                throw new \LogicException("No environment variables found for `$environment`.");
            }

            $extends = $envDefinition['extends'] ?? null;

            if ($extends !== null) {
                if (!isset($rawDefinition['environments'][$extends])) {
                    throw new \OutOfRangeException("Environment with name `$extends` not found.");
                }

                $variables = array_replace_recursive(
                    $rawDefinition['environments'][$extends]['variables'] ?? [],
                    $envDefinition['variables'],
                );
            }

            $envVariables = new EnvVariables($environment, $extends);

            foreach ($variables as $name => $definition) {
                $envVariables->add(
                    new Variable(
                        $name,
                        $definition['description'] ?? '',
                        $definition['export'] ?? false,
                        $definition['rules'] ?? [],
                        $definition['default'] ?? null,
                    ),
                );
            }

            $specification->add($envVariables);
        }

        return $specification;
    }
}
