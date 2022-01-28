<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Verification;

use Andriichuk\KeepEnv\Specification\Variable;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
interface VariableVerificationInterface
{
    /**
     * @param mixed $value
     *
     * @return VariableReport[]
     */
    public function validate(Variable $variable, $value): array;
}
