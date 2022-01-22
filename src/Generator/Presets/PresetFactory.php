<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Generator\Presets;

use OutOfRangeException;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class PresetFactory
{
    public function make(string $alias): PresetInterface
    {
        switch ($alias) {
            case 'laravel':
                return new LaravelPreset();

            case 'symfony':
                return new SymfonyPreset();

            default:
                throw new OutOfRangeException("Undefined preset with alias `$alias`.");
        }
    }
}
