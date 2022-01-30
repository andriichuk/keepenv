<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Validation;

use Andriichuk\KeepEnv\Specification\Variable;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
interface VariableValidationInterface
{
    /**
     * @param mixed $value
     *
     * @return VariableReport[]
     */
    public function validate(Variable $variable, $value): array;
}
