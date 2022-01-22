<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Unit\Specification;

use Andriichuk\KeepEnv\Specification\EnvVariables;
use Andriichuk\KeepEnv\Specification\Specification;
use Andriichuk\KeepEnv\Specification\Variable;
use PHPUnit\Framework\TestCase;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class SpecificationTest extends TestCase
{
    public function testEmpty(): void
    {
        $specification = Specification::default();

        $this->assertEquals(
            [
                'version' => '1.0',
                'environments' => [],
            ],
            $specification->toArray(),
        );
    }

    public function testBasicCreationWithVariables(): void
    {
        $specification = Specification::default();

        $productionEnvVariables = new EnvVariables('production');
        $productionEnvVariables->add(
            new Variable(
                'APP_ENV',
                'Application environment.',
                false,
                false,
                [
                    'required' => true,
                    'string' => true,
                    'equals' => 'production',
                ],
                'production',
            )
        );

        $localEnvVariables = new EnvVariables('local');
        $localEnvVariables->add(
            new Variable(
                'APP_ENV',
                'Application environment.',
                false,
                false,
                [
                    'required' => true,
                    'string' => true,
                    'equals' => 'local',
                ],
            ),
        );

        $specification->add($productionEnvVariables);
        $specification->add($localEnvVariables);

        $this->assertEquals(
            [
                'version' => '1.0',
                'environments' => [
                    'local' => [
                        'variables' => [
                            'APP_ENV' => [
                                'description' => 'Application environment.',
                                'rules' => [
                                    'required' => true,
                                    'string' => true,
                                    'equals' => 'local',
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
                                    'string' => true,
                                    'equals' => 'production',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            $specification->toArray(),
        );
    }
}
