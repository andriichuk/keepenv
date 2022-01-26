<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Specification;

use Andriichuk\KeepEnv\Specification\Exceptions\InvalidStructureException;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class SpecificationArrayBuilder implements SpecificationBuilderInterface
{
    public function build(array $rawDefinition): Specification
    {
        $version = (string) ($rawDefinition['version'] ?? '');

        if (empty($version)) {
            throw InvalidStructureException::missingVersion();
        }

        if (!isset($rawDefinition['environments'])) {
            throw InvalidStructureException::missingEnvironments();
        }

        if (!is_array($rawDefinition['environments']) || empty($rawDefinition['environments'])) {
            throw InvalidStructureException::invalidOrEmptyEnvironments();
        }

        $specification = new Specification($version);

        /**
         * @var string $environment
         * @var array $envDefinition
         */
        foreach ($rawDefinition['environments'] as $environment => $envDefinition) {
            $variables = $envDefinition['variables'] ?? null;

            if (empty($variables) || !is_array($variables)) {
                throw InvalidStructureException::missingVariables($environment);
            }

            $extends = $envDefinition['extends'] ?? null;

            if ($extends !== null) {
                if (!is_string($extends)) {
                    throw InvalidStructureException::extendsEnvNameIsNotString();
                }

                if (!isset($rawDefinition['environments'][$extends])) {
                    throw InvalidStructureException::extendsEnvironmentNotFound($extends);
                }

                if ($rawDefinition['environments'][$extends] === $environment) {
                    throw InvalidStructureException::extendsFromItself();
                }

                if (isset($rawDefinition['environments'][$extends]['extends'])) {
                    throw InvalidStructureException::nestedExtends();
                }

                if (!is_array($envDefinition['variables'])) {
                    throw InvalidStructureException::missingVariables($environment);
                }

                $variablesFromParentEnv = $rawDefinition['environments'][$extends]['variables'] ?? [];

                if (!is_array($variablesFromParentEnv)) {
                    throw InvalidStructureException::missingVariables($extends);
                }

                $variables = array_replace_recursive($variablesFromParentEnv, $envDefinition['variables']);
            }

            $envVariables = new EnvVariables($environment, $extends);

            /**
             * @var string $name
             * @var mixed $definition
             */
            foreach ($variables as $name => $definition) {
                if (empty($definition) || !is_array($definition)) {
                    throw InvalidStructureException::emptyOrInvalidVariableDefinition();
                }

                $description = $definition['description'] ?? '';

                if (!is_string($description)) {
                    throw InvalidStructureException::invalidVariableDefinition('Variable `description` field value must be a string.');
                }

                $export = $definition['export'] ?? false;

                if (!is_bool($export)) {
                    throw InvalidStructureException::invalidVariableDefinition('Variable `export` field value must be a boolean.');
                }

                $system = $definition['system'] ?? false;

                if (!is_bool($system)) {
                    throw InvalidStructureException::invalidVariableDefinition('Variable `system` field value must be a boolean.');
                }

                $rules = $definition['rules'] ?? [];

                if (!is_array($rules)) {
                    throw InvalidStructureException::invalidVariableDefinition('Variable `rules` field value must be an array.');
                }

                $envVariables->add(
                    new Variable($name, $description, $export, $system, $rules, $definition['default'] ?? null)
                );
            }

            $specification->add($envVariables);
        }

        return $specification;
    }
}
