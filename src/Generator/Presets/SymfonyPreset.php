<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Generator\Presets;

use Andriichuk\KeepEnv\Specification\Variable;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class SymfonyPreset implements PresetInterface
{
    public function provide(): array
    {
        return [
            'APP_ENV' => new Variable(
                'APP_ENV',
                'Application environment name.',
                false,
                false,
                [
                    'required' => true,
                    'enum' => ['dev', 'test', 'prod'],
                ],
                'dev',
            ),
            'APP_SECRET' => new Variable(
                'APP_SECRET',
                'Application secret.',
                false,
                false,
                [
                    'required' => true,
                ],
            ),
            'DATABASE_URL' => new Variable(
                'DATABASE_URL',
                'Database URL.',
                false,
                false,
                [
                    'required' => true,
                ],
            ),
        ];
    }
}
