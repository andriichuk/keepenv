<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Generation\Presets;

interface PresetInterface
{
    public function provide(): array;
}
