<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Unit\Validation;

use Andriichuk\KeepEnv\Validation\NumericValidator;
use Generator;
use PHPUnit\Framework\TestCase;

class NumericValidatorTest extends TestCase
{
    /**
     * @dataProvider validationCasesProvider
     */
    public function testValidationCases(string $subject, bool $expectedResult, string $message): void
    {
        $validator = new NumericValidator();
        $result = $validator->validate($subject, []);

        $this->assertEquals($expectedResult, $result, $message);
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
