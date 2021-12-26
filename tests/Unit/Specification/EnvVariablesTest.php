<?php

declare(strict_types=1);

namespace Andriichuk\Enviro\Unit\Specification;

use Andriichuk\Enviro\Specification\EnvVariables;
use Andriichuk\Enviro\Specification\Variable;
use PHPUnit\Framework\TestCase;

class EnvVariablesTest extends TestCase
{
    public function testEmpty(): void
    {
        $variables = new EnvVariables('locale');

        $this->assertEquals([], $variables->toArray());
    }

    public function testWithAddedVariable(): void
    {
        $variables = new EnvVariables('production');
        $variables->add(
            new Variable('APP_ENV', 'Application environment.')
        );

        $this->assertEquals(
            [
                'APP_ENV' => [
                    'description' => 'Application environment.',
                ]
            ],
            $variables->toArray(),
        );
    }

    public function testWithRemovedVariable(): void
    {
        $variables = new EnvVariables('production');
        $variables->add(
            new Variable('APP_ENV', 'Application environment.')
        );
        $variables->add(
            new Variable('APP_DEBUG', 'Application debug.')
        );
        $variables->remove('APP_ENV');

        $this->assertEquals(
            [
                'APP_DEBUG' => [
                    'description' => 'Application debug.',
                ],
            ],
            $variables->toArray(),
        );
    }
}
