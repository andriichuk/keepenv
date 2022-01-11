<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Unit\Specification;

use Andriichuk\KeepEnv\Specification\Exceptions\InvalidStructureException;
use Andriichuk\KeepEnv\Specification\SpecificationArrayBuilder;
use Generator;
use PHPUnit\Framework\TestCase;

class SpecificationArrayBuilderTest extends TestCase
{
    /**
     * @dataProvider exceptionSource
     */
    public function testThatBuilderThrowsAnException(array $structure, string $message): void
    {
        $this->expectException(InvalidStructureException::class);
        $this->expectExceptionMessage($message);

        (new SpecificationArrayBuilder())->build($structure);
    }

    public function exceptionSource(): Generator
    {
        yield [
            [],
            'The `version` field is required.',
        ];

        yield [
            ['version' => '1.0'],
            'The `environments` field is required.',
        ];

        yield [
            [
                'version' => '1.0',
                'environments' => 100,
            ],
            'The `environments` field is invalid or empty.',
        ];

        yield [
            [
                'version' => '1.0',
                'environments' => [],
            ],
            'The `environments` field is invalid or empty.',
        ];

        yield [
            [
                'version' => '1.0',
                'environments' => [
                    'common' => [],
                ],
            ],
            'The `common` environment has no variables. Please define them or remove environment definition.',
        ];

        yield [
            [
                'version' => '1.0',
                'environments' => [
                    'common' => [
                        'extends' => 'missing',
                        'variables' => [
                            'APP_ENV' => [],
                        ],
                    ],
                ],
            ],
            'No environment found with name `missing`.',
        ];

        yield [
            [
                'version' => '1.0',
                'environments' => [
                    'common' => [
                        'extends' => 'missing',
                        'variables' => [
                            'APP_ENV' => [],
                        ],
                    ],
                ],
            ],
            'No environment found with name `missing`.',
        ];

        yield [
            [
                'version' => '1.0',
                'environments' => [
                    'common' => [
                        'variables' => [
                            'APP_ENV' => [],
                        ],
                    ],
                ],
            ],
            'Variable definition is empty or invalid.',
        ];

        yield [
            [
                'version' => '1.0',
                'environments' => [
                    'common' => [
                        'variables' => [
                            'APP_ENV' => 'wrong',
                        ],
                    ],
                ],
            ],
            'Variable definition is empty or invalid.',
        ];
    }

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
