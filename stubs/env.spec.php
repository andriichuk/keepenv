<?php

declare(strict_types=1);

return [
    'common' => [
        'APP_ENV' => [
            'description' => 'Application environment',
            'default' => 'production',
            'rules' => [
                'required',
                'enum' => [
                    'strict' => true,
                    'cases' => ['local', 'production'],
                ],
            ],
        ],
        'APP_DEBUG' => [
            'description' => 'Application debug mode.',
            'default' => 'true',
            'rules' => [
                'required',
                'enum' => [
                    'strict' => true,
                    'cases' => ['true', 'false'],
                ],
            ],
        ],
        'LOG_CHANNEL' => [
            'description' => 'Log channel.',
            'default' => 'stack',
            'rules' => [
                'required',
                'enum' => [
                    'strict' => true,
                    'cases' => ['stack', 'daily'],
                ],
            ],
        ],
    ],
    'local' => [

    ],
    'production' => [
        'APP_DEBUG' => [
            'rules' => [
                'equals' => 'false',
            ],
        ],
    ],
];
