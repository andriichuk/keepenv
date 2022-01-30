<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Tests\Unit\Utils;

use Andriichuk\KeepEnv\Utils\Stringify;
use Generator;
use JsonException;
use PHPUnit\Framework\TestCase;

class StringifyTest extends TestCase
{
    /**
     * @dataProvider typesDataProvider
     * @param mixed $value
     * @param mixed $expectedValue
     */
    public function testServiceCanPresentTypeAsString($value, $expectedValue, string $message): void
    {
        $service = new Stringify();

        $this->assertEquals($expectedValue, $service->format($value), $message);
    }

    public function typesDataProvider(): Generator
    {
        yield [
            'value' => 'string',
            'expectedValue' => 'string',
            'message' => 'Regular string',
        ];

        yield [
            'value' => 123,
            'expectedValue' => '123',
            'message' => 'Integer number',
        ];

        yield [
            'value' => true,
            'expectedValue' => 'true',
            'message' => 'Boolean value',
        ];

        yield [
            'value' => null,
            'expectedValue' => 'null',
            'message' => 'Nullable value',
        ];

        yield [
            'value' => ['key' => 'value'],
            'expectedValue' => '{"key":"value"}',
            'message' => 'Array value',
        ];
    }

    public function testServiceThrowsExceptionOnInvalidJsonValue(): void
    {
        $this->expectException(JsonException::class);
        $this->expectExceptionMessage('Type is not supported');

        $service = new Stringify();

        $service->format(fopen('php://input', 'rb'));
    }
}
