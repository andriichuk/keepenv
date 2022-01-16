<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Generator\Presets;

interface PresetInterface
{
    public function provide(): array;
}
