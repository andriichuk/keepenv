<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Tests\Unit\Validation\Rules;

use Andriichuk\KeepEnv\Validation\Rules\BooleanRule;
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
     */
    public function testValidationCases($subject, bool $expectedResult, string $message): void
    {
        $validator = new BooleanRule();
        $result = $validator->validate($subject, true);

        $this->assertEquals($expectedResult, $result, $message);

        if (!$result) {
            $this->assertEquals('The value must be a boolean.', $validator->message([]));
        }
    }

    public function validationCasesProvider(): Generator
    {
        yield [
            'subject' => 'true',
            'expected_result' => true,
            'message' => 'True as a string',
        ];

        yield [
            'subject' => 'Off',
            'expected_result' => true,
            'message' => 'Off string',
        ];

        yield [
            'subject' => 'yES',
            'expected_result' => true,
            'message' => 'Yes string',
        ];

        yield [
            'subject' => '0',
            'expected_result' => true,
            'message' => 'String integer zero',
        ];

        yield [
            'subject' => true,
            'expected_result' => true,
            'message' => 'Native boolean',
        ];

        yield [
            'subject' => '',
            'expected_result' => false,
            'message' => 'Empty string',
        ];

        yield [
            'subject' => '777',
            'expected_result' => false,
            'message' => 'Number different from 1/0',
        ];

        yield [
            'subject' => 'OnOFf',
            'expected_result' => false,
            'message' => 'String different from true/false, on/off',
        ];
    }
}
