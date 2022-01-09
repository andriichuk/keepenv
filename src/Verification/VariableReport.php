<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Verification;

/**
 * @psalm-immutable
 */
class VariableReport
{
    public string $variable;
    public string $message;

    public function __construct(string $variable, string $message)
    {
        $this->variable = $variable;
        $this->message = $message;
    }
}
