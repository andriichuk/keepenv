<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Verification;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class VerificationReport
{
    /**
     * @var VariableReport[]
     */
    private array $reports = [];
    private int $variablesCount = 0;

    public function add(VariableReport $report): void
    {
        $this->reports[] = $report;
    }

    public function isEmpty(): bool
    {
        return $this->reports === [];
    }

    public function errorsCount(): int
    {
        return count($this->reports);
    }

    public function all(): array
    {
        return $this->reports;
    }

    public function variablesCount(): int
    {
        return $this->variablesCount;
    }

    public function setVariablesCount(int $variablesCount): void
    {
        $this->variablesCount = $variablesCount;
    }
}
