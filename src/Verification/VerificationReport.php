<?php

declare(strict_types=1);

namespace Andriichuk\Enviro\Verification;

class VerificationReport
{
    /**
     * @var VariableReport[]
     */
    private array $reports = [];

    public function add(VariableReport $report): void
    {
        $this->reports[] = $report;
    }

    public function isEmpty(): bool
    {
        return $this->reports === [];
    }

    public function count(): int
    {
        return count($this->reports);
    }

    public function all(): array
    {
        return $this->reports;
    }
}
