<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Tests\Unit\Validation\Rules;

use Generator;
use PHPUnit\Framework\TestCase;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class NumericRuleTest extends TestCase
{
    /**
     * @dataProvider validationCasesProvider
     *
     * @param string|int $subject
     */
    public function testValidationCases($subject, bool $expectedResult, string $message): void
    {
        $validator = new \Andriichuk\KeepEnv\Validation\Rules\NumericRule();
        $result = $validator->validate($subject, true);

        $this->assertEquals($expectedResult, $result, $message);

        if (!$result) {
            $this->assertEquals('The value must be a numeric.', $validator->message([]));
        }
    }

    public function validationCasesProvider(): Generator
    {
        yield [
            'subject' => '0',
            'expected_result' => true,
            'message' => 'Valid integer string 1',
        ];

        yield [
            'subject' => '100',
            'expected_result' => true,
            'message' => 'Valid integer string 2',
        ];

        yield [
            'subject' => '04343',
            'expected_result' => true,
            'message' => 'Valid integer string 3',
        ];

        yield [
            'subject' => 123,
            'expected_result' => true,
            'message' => 'Valid integer string 4',
        ];

        yield [
            'subject' => '-100',
            'expected_result' => true,
            'message' => 'Valid negative integer string',
        ];

        yield [
            'subject' => '1337e0',
            'expected_result' => true,
            'message' => 'Valid number',
        ];

        yield [
            'subject' => '1337.0',
            'expected_result' => true,
            'message' => 'Valid float string',
        ];

        yield [
            'subject' => '0x237',
            'expected_result' => false,
            'message' => 'Invalid numeric 1',
        ];

        yield [
            'subject' => 'numeric',
            'expected_result' => false,
            'message' => 'String expression',
        ];

        yield [
            'subject' => '',
            'expected_result' => false,
            'message' => 'Empty string',
        ];
    }
}
