<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Generation\Presets;

use OutOfRangeException;

class PresetFactory
{
    public function make(string $alias): PresetInterface
    {
        switch ($alias) {
            case 'laravel':
                return new LaravelPreset();

            default:
                throw new OutOfRangeException("Undefined preset with alias `$alias`.");
        }
    }
}
