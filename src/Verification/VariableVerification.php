<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Verification;

use Andriichuk\KeepEnv\Specification\Variable;
use Andriichuk\KeepEnv\Validation\RulesRegistryInterface;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class VariableVerification
{
    private RulesRegistryInterface $rulesRegistry;

    public function __construct(RulesRegistryInterface $rulesRegistry)
    {
        $this->rulesRegistry = $rulesRegistry;
    }

    /**
     * @param mixed $value
     *
     * @return VariableReport[]
     */
    public function validate(Variable $variable, $value): array
    {
        $report = [];

        if ($variable->rules === []) {
            return $report;
        }

        /**
         * @var string $ruleName
         * @var array|scalar|null $options
         */
        foreach ($variable->rules as $ruleName => $options) {
            $validator = $this->rulesRegistry->get($ruleName);

            if (!$validator->acceptsFalseOption() && is_bool($options) && !$options) {
                continue;
            }

            $isValid = $validator->validate($value, $options);

            if (!$isValid) {
                $report[] = new VariableReport(
                    $variable->name,
                    $validator->message([
                        'value' => $value,
                        'name' => $variable->name,
                        'equals' => $variable->rules['equals'] ?? '',
                        'cases' => $options,
                        'min' => $options['min'] ?? null,
                        'max' => $options['max'] ?? null,
                    ]),
                );
            }
        }

        return $report;
    }
}
