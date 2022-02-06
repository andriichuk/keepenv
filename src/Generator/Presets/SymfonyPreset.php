<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Generator\Presets;

use Andriichuk\KeepEnv\Specification\Variable;

/**
 * TODO: Analyze and describe all typical environment variables for Symfony app
 *
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
            'TRUSTED_PROXIES' => new Variable(
                'TRUSTED_PROXIES',
                'Trusted proxies.',
                false,
                false,
                [],
                '127.0.0.0/8,10.0.0.0/8,172.16.0.0/12,192.168.0.0/16'
            ),
            'TRUSTED_HOSTS' => new Variable(
                'TRUSTED_HOSTS',
                'Trusted hosts.',
                false,
                false,
                [],
                '"^(localhost|example\.com)$"'
            ),
            'MAILER_DSN' => new Variable(
                'MAILER_DSN',
                'Mailer DSN.',
                false,
                false,
                [],
                'smtp://localhost'
            ),
        ];
    }
}
