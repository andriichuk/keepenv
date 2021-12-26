<?php

declare(strict_types=1);

namespace Andriichuk\Enviro\Verification;

use Andriichuk\Enviro\Reader\Specification\SpecificationReaderInterface;
use Andriichuk\Enviro\State\EnvStateProviderInterface;
use Andriichuk\Enviro\Validation\ValidatorRegistryInterface;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class SpecVerificationService
{
    private EnvStateProviderInterface $environmentStateProvider;
    private SpecificationReaderInterface $specificationReader;
    private ValidatorRegistryInterface $validatorRegistry;

    public function __construct(
        EnvStateProviderInterface $environmentStateProvider,
        SpecificationReaderInterface $specificationReader,
        ValidatorRegistryInterface $validatorRegistry
    ) {
        $this->environmentStateProvider = $environmentStateProvider;
        $this->specificationReader = $specificationReader;
        $this->validatorRegistry = $validatorRegistry;
    }

    public function verify(string $source, string $envName): array
    {
        $specification = $this->specificationReader->read($source)->get($envName);
        $messages = [];

        foreach ($specification->all() as $variable) {
            if ($variable->rules === null) {
                continue;
            }

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
