<?php

declare(strict_types=1);

namespace Andriichuk\Enviro\Verification;

use Andriichuk\Enviro\Specification\Variable;
use Andriichuk\Enviro\Validation\ValidatorRegistryInterface;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class VariableVerification
{
    private ValidatorRegistryInterface $validatorRegistry;

    public function __construct(ValidatorRegistryInterface $validatorRegistry)
    {
        $this->validatorRegistry = $validatorRegistry;
    }

    /**
     * @param mixed $value
     *
     * @return VariableReport[]
     */
    public function validate(Variable $variable, $value): array
    {
        $report = [];

        if ($variable->rules === null) {
            return $report;
        }

        foreach ($variable->rules as $ruleName => $options) {
            $ruleName = is_string($ruleName) ? $ruleName : $options;
            $validator = $this->validatorRegistry->get($ruleName);

            $isValid = $validator->validate($value, is_array($options) ? $options : [$options]);

            if (!$isValid) {
                $report[] = new VariableReport(
                    $variable->name,
                    $validator->message([
                        'name' => $variable->name,
                        'value' => $value,
                        'cases' => $options,
                        'equals' => $variable->rules['equals'] ?? '',
                    ]),
                );
            }
        }

        return $report;
    }
}
