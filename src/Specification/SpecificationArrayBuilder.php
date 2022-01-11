<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Specification;

use Andriichuk\KeepEnv\Specification\Exceptions\InvalidStructureException;

class SpecificationArrayBuilder implements SpecificationBuilderInterface
{
    public function build(array $rawDefinition): Specification
    {
        $version = $rawDefinition['version'] ?? null;

        if ($version === null) {
            throw InvalidStructureException::missingVersion();
        }

        if (!isset($rawDefinition['environments'])) {
            throw InvalidStructureException::missingEnvironments();
        }

        if (!is_array($rawDefinition['environments']) || empty($rawDefinition['environments'])) {
            throw InvalidStructureException::invalidOrEmptyEnvironments();
        }

        $specification = new Specification($version);

        foreach ($rawDefinition['environments'] as $environment => $envDefinition) {
            $variables = $envDefinition['variables'] ?? null;

            if (empty($variables)) {
                throw InvalidStructureException::missingVariables($environment);
            }

            $extends = $envDefinition['extends'] ?? null;

            if ($extends !== null) {
                if (!isset($rawDefinition['environments'][$extends])) {
                    throw InvalidStructureException::extendsEnvironmentNotFound($extends);
                }

                if ($rawDefinition['environments'][$extends] === $environment) {
                    throw InvalidStructureException::extendsFromItself();
                }

                if (isset($rawDefinition['environments'][$extends]['extends'])) {
                    throw InvalidStructureException::nestedExtends();
                }

                $variables = array_replace_recursive(
                    $rawDefinition['environments'][$extends]['variables'] ?? [],
                    $envDefinition['variables'],
                );
            }

            $envVariables = new EnvVariables($environment, $extends);

            foreach ($variables as $name => $definition) {
                if (empty($definition) || !is_array($definition)) {
                    throw InvalidStructureException::emptyOrInvalidVariableDefinition();
                }

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
