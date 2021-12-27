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
        ],
        'local' => [
            'MAIL_HOST' => [
                'description' => 'Main host.',
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
