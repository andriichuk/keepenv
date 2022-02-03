<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Tests\Unit\Validation\Rules;

use Andriichuk\KeepEnv\Validation\Rules\BooleanRule;
use Andriichuk\KeepEnv\Validation\Rules\Exceptions\RuleOptionsException;
use Generator;
use PHPUnit\Framework\TestCase;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class BooleanRuleTest extends TestCase
{
    /**
     * @dataProvider validationCasesProvider
     *
     * @param string|bool|int $subject
     * @param bool|array $options
     */
    public function testValidationCases($subject, $options, bool $expectedResult, string $message): void
    {
        $validator = new BooleanRule();
        $result = $validator->validate($subject, $options);

        $this->assertEquals($expectedResult, $result, $message);

        if (!$result) {
            $this->assertEquals('The value must be a boolean.', $validator->message([]));
        }
    }

    public function validationCasesProvider(): Generator
    {
        yield [
            'subject' => 'true',
            'options' => true,
            'expected_result' => true,
            'message' => 'True as a string',
        ];

        yield [
            'subject' => 'Off',
            'options' => true,
            'expected_result' => true,
            'message' => 'Off string',
        ];

        yield [
            'subject' => 'yES',
            'options' => true,
            'expected_result' => true,
            'message' => 'Yes string',
        ];

        yield [
            'subject' => '0',
            'options' => true,
            'expected_result' => true,
            'message' => 'String integer zero',
        ];

        yield [
            'subject' => true,
            'options' => true,
            'expected_result' => true,
            'message' => 'Native boolean',
        ];

        yield [
            'subject' => 'Y',
            'options' => ['true' => 'Y', 'false' => 'N'],
            'expected_result' => true,
            'message' => 'Array options with not boolean',
        ];

        yield [
            'subject' => '',
            'options' => true,
            'expected_result' => false,
            'message' => 'Empty string',
        ];

        yield [
            'subject' => '777',
            'options' => true,
            'expected_result' => false,
            'message' => 'Number different from 1/0',
        ];

        yield [
            'subject' => 'OnOFf',
            'options' => true,
            'expected_result' => false,
            'message' => 'String different from true/false, on/off',
        ];

        yield [
            'subject' => 'Y',
            'options' => ['true' => 'Yes', 'false' => 'No'],
            'expected_result' => false,
            'message' => 'Array options with no match',
        ];

        yield [
            'subject' => 0,
            'options' => ['true' => '1', 'false' => '0'],
            'expected_result' => false,
            'message' => 'Array options with different type',
        ];
    }

    /**
     * @dataProvider failedOptionsProvider
     */
    public function testRuleCanThrowExceptionOnInvalidOptions(array $options): void
    {
        $this->expectException(RuleOptionsException::class);

        $validator = new BooleanRule();
        $validator->validate('Yes', $options);
    }

    public function failedOptionsProvider(): Generator
    {
        yield [
            [],
        ];

        yield [
            ['true' => ''],
        ];

        yield [
            ['true' => '', 'false' => 'No']
        ];

        yield [
            ['true' => null, 'false' => null]
        ];

        yield [
            ['true' => 'Yes', 'false' => 'Yes']
        ];
    }
}
