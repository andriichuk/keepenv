<?php

declare(strict_types=1);

namespace Andriichuk\Enviro\Verification;

use Andriichuk\Enviro\Specification\SpecificationLoader;
use Andriichuk\Enviro\State\EnvStateProviderInterface;
use Andriichuk\Enviro\Validation\ValidatorRegistryInterface;

class SpecVerificationService
{
    private EnvStateProviderInterface $environmentStateProvider;
    private SpecificationLoader $specificationLoader;
    private ValidatorRegistryInterface $validatorRegistry;

    public function __construct(
        EnvStateProviderInterface    $environmentStateProvider,
        SpecificationLoader $specificationLoader,
        ValidatorRegistryInterface   $validatorRegistry
    ) {
        $this->environmentStateProvider = $environmentStateProvider;
        $this->specificationLoader = $specificationLoader;
        $this->validatorRegistry = $validatorRegistry;
    }

    public function verify(string $environment): array
    {
        $specification = $this->specificationLoader->load($environment);
        $messages = [];

        foreach ($specification->all() as $variable) {
            foreach ($variable->rules as $ruleName => $options) {
                $ruleName = is_string($ruleName) ? $ruleName : $options;
                $validator = $this->validatorRegistry->get($ruleName);

                $isValid = $validator->validate(
                    $this->environmentStateProvider->get($variable->name),
                    is_array($options) ? $options : [$options],
                );

                if (!$isValid) {
                    $messages[] = "{$variable->name} violates the rule `{$ruleName}`. {$validator->message()}";
                }
            }
        }

        return $messages;
    }
}
