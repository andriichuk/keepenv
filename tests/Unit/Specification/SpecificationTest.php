<?php

declare(strict_types=1);

namespace Andriichuk\Enviro\Unit\Specification;

use Andriichuk\Enviro\Specification\EnvVariables;
use Andriichuk\Enviro\Specification\Specification;
use Andriichuk\Enviro\Specification\Variable;
use PHPUnit\Framework\TestCase;

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
                [
                    'required' => true,
                    'string' => true,
                    'equals' => 'production',
                ],
                'production'
            )
        );

        $localEnvVariables = new EnvVariables('local');
        $localEnvVariables->add(
            new Variable(
                'APP_ENV',
                'Application environment.',
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
                        'APP_ENV' => [
                            'description' => 'Application environment.',
                            'rules' => [
                                'required' => true,
                                'string' => true,
                                'equals' => 'local',
                            ],
                        ],
                    ],
                    'production' => [
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
            $specification->toArray(),
        );
    }
}
