<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Unit\Validation\Rules;

use Andriichuk\KeepEnv\Validation\Exceptions\RuleOptionsException;
use Andriichuk\KeepEnv\Validation\Rules\StringRule;
use Generator;
use PHPUnit\Framework\TestCase;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class StringRuleTest extends TestCase
{
    /**
     * @dataProvider validationCasesProvider
     *
     * @param mixed $subject
     * @param bool|array $options
     */
    public function testValidationCases($subject, $options, bool $expectedResult, string $expectedMessage, string $message): void
    {
        $validator = new StringRule();
        $result = $validator->validate($subject, $options);

        $this->assertEquals($expectedResult, $result, $message);

        if (!$result) {
            $this->assertEquals(
                $expectedMessage,
                $validator->message([
                    'min' => $options['min'] ?? null,
                    'max' => $options['max'] ?? null,
                ])
            );
        }
    }

    public function validationCasesProvider(): Generator
    {
        yield [
            'subject' => '123qwe',
            'options' => true,
            'expected_result' => true,
            'expected_message' => '',
            'message' => 'Valid string',
        ];

        yield [
            'subject' => '123',
            'options' => true,
            'expected_result' => true,
            'expected_message' => '',
            'message' => 'Valid numeric string',
        ];

        yield [
            'subject' => 'qwe',
            'options' => ['min' => 3],
            'expected_result' => true,
            'expected_message' => '',
            'message' => 'Valid min length',
        ];

        yield [
            'subject' => 'qwe',
            'options' => ['max' => 4],
            'expected_result' => true,
            'expected_message' => '',
            'message' => 'Valid max length',
        ];

        yield [
            'subject' => 'qwe',
            'options' => ['min' => 1, 'max' => 4],
            'expected_result' => true,
            'expected_message' => '',
            'message' => 'Valid min/max range',
        ];

        yield [
            'subject' => '',
            'options' => ['min' => 0],
            'expected_result' => true,
            'expected_message' => '',
            'message' => 'Valid empty string with range',
        ];

        yield [
            'subject' => true,
            'options' => true,
            'expected_result' => false,
            'expected_message' => 'The value must be a string.',
            'message' => 'Invalid with boolean',
        ];

        yield [
            'subject' => 'qwe',
            'options' => ['min' => 4],
            'expected_result' => false,
            'expected_message' => 'The value length must be greater or equal 4.',
            'message' => 'Invalid string min length',
        ];

        yield [
            'subject' => 'qwe',
            'options' => ['max' => 2],
            'expected_result' => false,
            'expected_message' => 'The value length must be lower or equal 2.',
            'message' => 'Invalid string max length',
        ];

        yield [
            'subject' => 'qwe12345',
            'options' => ['min' => 2, 'max' => 4],
            'expected_result' => false,
            'expected_message' => 'The value length must be between 2 and 4.',
            'message' => 'Invalid string length range',
        ];
    }

    /**
     * @dataProvider invalidOptionsProvider
     * @param mixed $options
     */
    public function testRuleThrowsExceptionOnInvalidOption($options): void
    {
        $this->expectException(RuleOptionsException::class);

        $validator = new StringRule();
        $validator->validate('qwerty', $options);
    }

    public function invalidOptionsProvider(): Generator
    {
        yield [null];

        yield [[]];

        yield [['min' => null, 'max' => null]];

        yield [['min' => 10, 'max' => 5]];
    }
}
