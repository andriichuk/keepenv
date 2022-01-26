<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Unit\Specification;

use Andriichuk\KeepEnv\Specification\Exceptions\InvalidStructureException;
use Andriichuk\KeepEnv\Specification\SpecificationArrayBuilder;
use Generator;
use PHPUnit\Framework\TestCase;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
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
                'version' => '222.0',
                'environments' => [
                    'common' => [
                        'variables' => [
                            'APP_ENV' => [
                                'description' => 'Application environment.',
                            ],
                        ],
                    ],
                ],
            ],
            'Unsupported version of specification file.',
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

        yield [
            [
                'version' => '1.0',
                'environments' => [
                    'local' => [
                        'extends' => 123,
                        'variables' => [
                            'APP_ENV' => [
                                'description' => 'Application environment.',
                            ],
                        ],
                    ],
                ],
            ],
            'Environment name in `extends` field must be a string.',
        ];

        yield [
            [
                'version' => '1.0',
                'environments' => [
                    'common' => [],
                    'local' => [
                        'extends' => 'common',
                        'variables' => [
                            'APP_ENV' => [
                                'description' => 'Application environment.',
                            ],
                        ],
                    ],
                ],
            ],
            'The `common` environment has no variables. Please define them or remove environment definition.',
        ];

        yield [
            [
                'version' => '1.0',
                'environments' => [
                    'common' => [
                        'variables' => [
                            'APP_ENV' => [
                                'description' => 'Application environment.',
                            ],
                        ],
                    ],
                    'local' => [
                        'extends' => 'common',
                        'variables' => [
                            'APP_DEBUG' => [
                                'description' => 'Application debug.',
                            ],
                        ]
                    ],
                    'production' => [
                        'extends' => 'local',
                        'variables' => [
                            'APP_LOG' => [
                                'description' => 'Application logger.',
                            ],
                        ],
                    ],
                ],
            ],
            'Nested extending is not supported yet. You can extend only from one parent.',
        ];

        yield [
            [
                'version' => '1.0',
                'environments' => [
                    'local' => [
                        'variables' => [
                            'APP_ENV' => [
                                'description' => true,
                            ],
                        ],
                    ],
                ],
            ],
            'Invalid variable definition. Variable `description` field value must be a string.',
        ];

        yield [
            [
                'version' => '1.0',
                'environments' => [
                    'local' => [
                        'variables' => [
                            'APP_ENV' => [
                                'export' => 'true',
                            ],
                        ],
                    ],
                ],
            ],
            'Invalid variable definition. Variable `export` field value must be a boolean.',
        ];

        yield [
            [
                'version' => '1.0',
                'environments' => [
                    'local' => [
                        'variables' => [
                            'APP_ENV' => [
                                'system' => 'false',
                            ],
                        ],
                    ],
                ],
            ],
            'Invalid variable definition. Variable `system` field value must be a boolean.',
        ];

        yield [
            [
                'version' => '1.0',
                'environments' => [
                    'local' => [
                        'variables' => [
                            'APP_ENV' => [
                                'rules' => 'array',
                            ],
                        ],
                    ],
                ],
            ],
            'Invalid variable definition. Variable `rules` field value must be an array.',
        ];
    }

    /**
     * @dataProvider environmentsSource
     */
    public function testThatBuilderCanParseEnvironments(string $message, array $source): void
    {
        $builder = new SpecificationArrayBuilder();
        $specification = $builder->build($source);

        $this->assertEquals($source, $specification->toArray(), $message);
    }

    public function environmentsSource(): Generator
    {
        yield [
            'message' => 'Single environment structure.',
            'source' => [
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
                        ],
                    ],
                ],
            ],
        ];

        yield [
            'message' => 'Two environments with the same key.',
            'source' => [
                'version' => '1.0',
                'environments' => [
                    'local' => [
                        'variables' => [
                            'APP_ENV' => [
                                'description' => 'Application environment.',
                                'default' => 'local',
                                'rules' => [
                                    'required' => true,
                                    'enum' => ['local', 'production'],
                                ],
                            ],
                        ],
                    ],
                    'production' => [
                        'variables' => [
                            'APP_ENV' => [
                                'description' => 'Application environment.',
                                'default' => 'production',
                                'rules' => [
                                    'required' => true,
                                    'enum' => ['local', 'production'],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        yield [
            'message' => 'Two environments with the different keys.',
            'source' => [
                'version' => '1.0',
                'environments' => [
                    'local' => [
                        'variables' => [
                            'APP_ENV' => [
                                'description' => 'Application environment.',
                                'default' => 'local',
                                'rules' => [
                                    'required' => true,
                                    'enum' => ['local', 'production'],
                                ],
                            ],
                        ],
                    ],
                    'production' => [
                        'variables' => [
                            'APP_DEBUG' => [
                                'description' => 'Application debug.',
                                'default' => 'false',
                                'rules' => [
                                    'required' => true,
                                    'enum' => ['true', 'false'],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        yield [
            'message' => 'Two environments with the different keys and extends.',
            'source' => [
                'version' => '1.0',
                'environments' => [
                    'local' => [
                        'variables' => [
                            'APP_ENV' => [
                                'description' => 'Application environment.',
                                'default' => 'local',
                                'rules' => [
                                    'required' => true,
                                    'enum' => ['local', 'production'],
                                ],
                            ],
                        ],
                    ],
                    'production' => [
                        'extends' => 'local',
                        'variables' => [
                            'APP_DEBUG' => [
                                'description' => 'Application debug.',
                                'default' => 'false',
                                'rules' => [
                                    'required' => true,
                                    'enum' => ['true', 'false'],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        yield [
            'message' => 'Two environments with the same keys and extends.',
            'source' => [
                'version' => '1.0',
                'environments' => [
                    'local' => [
                        'variables' => [
                            'APP_ENV' => [
                                'description' => 'Application environment.',
                                'default' => 'local',
                                'rules' => [
                                    'required' => true,
                                    'enum' => ['local', 'production'],
                                ],
                            ],
                        ],
                    ],
                    'production' => [
                        'extends' => 'local',
                        'variables' => [
                            'APP_ENV' => [
                                'default' => 'production',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        yield [
            'message' => 'Multiple environments with extends.',
            'source' => [
                'version' => '1.0',
                'environments' => [
                    'common' => [
                        'variables' => [
                            'APP_ENV' => [
                                'description' => 'Application environment.',
                                'default' => 'local',
                                'rules' => [
                                    'required' => true,
                                    'enum' => ['local', 'production'],
                                ],
                            ],
                            'APP_DEBUG' => [
                                'description' => 'Application debug.',
                                'default' => 'false',
                                'rules' => [
                                    'required' => true,
                                    'enum' => ['true', 'false'],
                                ],
                            ],
                        ],
                    ],
                    'local' => [
                        'extends' => 'common',
                        'variables' => [
                            'APP_ENV' => [
                                'rules' => [
                                    'equals' => 'local',
                                ],
                            ],
                            'LOCAL_MAIL' => [
                                'description' => 'Local mail.',
                                'default' => 'on',
                                'rules' => [
                                    'required' => false,
                                    'enum' => ['on', 'off'],
                                ],
                            ],
                        ],
                    ],
                    'production' => [
                        'extends' => 'common',
                        'variables' => [
                            'APP_ENV' => [
                                'rules' => [
                                    'equals' => 'production',
                                ],
                            ],
                            'APP_PROFILE' => [
                                'description' => 'Application profile.',
                                'default' => 'on',
                                'rules' => [
                                    'required' => true,
                                    'enum' => ['on', 'off'],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
