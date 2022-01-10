<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Unit\Specification;

use Andriichuk\KeepEnv\Specification\SpecificationArrayBuilder;
use PHPUnit\Framework\TestCase;

class SpecificationArrayBuilderTest extends TestCase
{
    public function testBuilder(): void
    {
        $builder = new SpecificationArrayBuilder();

        $rawDefinition = [
            'version' => '1.0',
            'environments' => [
                'common' => [
                    'variables' => [
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
                            'rules' => [
                                'required' => true,
                                'enum' => ['mailhog', 'mailgun'],
                            ],
                        ],
                    ],
                ],
                'local' => [
                    'extends' => 'common',
                    'variables' => [
                        'MAIL_HOST' => [
                            'rules' => [
                                'equals' => 'mailhog',
                            ],
                        ],
                    ],
                ],
                'production' => [
                    'extends' => 'common',
                    'variables' => [
                        'APP_DEBUG' => [
                            'rules' => [
                                'equals' => 'false',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $specification = $builder->build($rawDefinition);

        $this->assertEquals($rawDefinition, $specification->toArray());
    }
}
