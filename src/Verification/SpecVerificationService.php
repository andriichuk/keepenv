<?php

declare(strict_types=1);

namespace Andriichuk\Enviro\Verification;

use Andriichuk\Enviro\Reader\SpecificationReaderInterface;
use Andriichuk\Enviro\State\EnvStateProviderInterface;
use Andriichuk\Enviro\Validation\ValidatorRegistryInterface;

class SpecVerificationService
{
    private EnvStateProviderInterface $environmentStateProvider;
    private SpecificationReaderInterface $environmentSpecProvider;
    private ValidatorRegistryInterface $validatorRegistry;

    public function __construct(
        EnvStateProviderInterface    $environmentStateProvider,
        SpecificationReaderInterface $environmentSpecProvider,
        ValidatorRegistryInterface   $validatorRegistry
    ) {
        $this->environmentStateProvider = $environmentStateProvider;
        $this->environmentSpecProvider = $environmentSpecProvider;
        $this->validatorRegistry = $validatorRegistry;
    }

    public function verify(string $environment)
    {
        $common = $this->environmentSpecProvider->read()['common'] ?? [];
        $environmentSpecification = $this->environmentSpecProvider->read()[$environment] ?? null;

        if ($environmentSpecification === null) {
            throw new \InvalidArgumentException("Environment with the name `{$environment}` is not exists.");
        }

        $environmentSpecification = array_replace_recursive(
            $common,
            $environmentSpecification,
        );

        $messages = [];

        foreach ($environmentSpecification as $variableName => $specification) {
            foreach ($specification['rules'] ?? [] as $ruleName => $options) {
                $ruleName = is_string($ruleName) ? $ruleName : $options;
                $validator = $this->validatorRegistry->get($ruleName);

                $isValid = $validator->validate(
                    $this->environmentStateProvider->get($variableName),
                    is_array($options) ? $options : [$options],
                );

                if (!$isValid) {
                    $messages[] = "{$variableName} violates the rule `{$ruleName}`. {$validator->message()}";
                }
            }
        }

        return $messages;
    }
}
