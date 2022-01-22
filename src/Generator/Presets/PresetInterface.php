<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Generator\Presets;

use Andriichuk\KeepEnv\Specification\Variable;

interface PresetInterface
{
    /**
     * @return array<string, Variable>
     */
    public function provide(): array;
}
