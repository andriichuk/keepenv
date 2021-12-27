<?php

return [
    'version' => '1.0',
    'environments' => [
        'common' => [
            'APP_ENV' => [
                'description' => 'Application environment',
                'default' => 'production',
                'rules' => [
                    'required' => true,
                    'enum' => ['local', 'production'],
                ],
            ],
            'APP_DEBUG' => [
                'description' => 'Application debug mode.',
                'default' => 'true',
                'rules' => [
                    'required' => true,
                    'enum' => ['true', 'false'],
                ],
            ],
            'LOG_CHANNEL' => [
                'description' => 'Log channel.',
                'default' => 'stack',
                'rules' => [
                    'required' => true,
                    'enum' => ['stack', 'daily'],
                ],
            ],
            'MAIL_HOST' => [
                'required' => true,
                'enum' => ['mailhog', 'mailgun'],
            ],
        ],
        'local' => [
            'MAIL_HOST' => [
                'rules' => [
                    'equals' => 'mailhog',
                ],
            ],
        ],
        'production' => [
            'APP_DEBUG' => [
                'rules' => [
                    'equals' => 'false',
                ],
            ],
        ],
    ],
];
