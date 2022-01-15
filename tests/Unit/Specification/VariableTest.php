<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Unit\Specification;

use Andriichuk\KeepEnv\Specification\Variable;
use PHPUnit\Framework\TestCase;

class VariableTest extends TestCase
{
    public function testVariableWithoutRules(): void
    {
        $variable = new Variable(
            'APP_ENV',
            'Application environment.',
        );

        $this->assertEquals(
            [
                'description' => 'Application environment.',
            ],
            $variable->toArray()
        );
    }

    public function testStringVariable(): void
    {
        $variable = new Variable(
            'APP_ENV',
            'Application environment.',
            false,
            false,
            [
                'string' => true,
            ]
        );

        $this->assertEquals(
            [
                'description' => 'Application environment.',
                'rules' => [
                    'string' => true,
                ]
            ],
            $variable->toArray()
        );
    }

    public function testRequiredVariable(): void
    {
        $variable = new Variable(
            'APP_ENV',
            'Application environment.',
            false,
            false,
            [
                'required' => true,
                'enum' => ['local', 'production'],
            ]
        );

        $this->assertEquals(
            [
                'description' => 'Application environment.',
                'rules' => [
                    'required' => true,
                    'enum' => ['local', 'production'],
                ]
            ],
            $variable->toArray()
        );
    }

    public function testRequiredVariableWithDefaultValue(): void
    {
        $variable = new Variable(
            'APP_ENV',
            'Application environment.',
            true,
            true,
            [
                'required' => true,
                'enum' => ['local', 'production'],
            ],
            'production',
        );

        $this->assertEquals(
            [
                'description' => 'Application environment.',
                'default' => 'production',
                'export' => true,
                'system' => true,
                'rules' => [
                    'required' => true,
                    'enum' => ['local', 'production'],
                ]
            ],
            $variable->toArray()
        );
    }
}
