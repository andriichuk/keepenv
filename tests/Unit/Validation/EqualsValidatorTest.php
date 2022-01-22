<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Unit\Validation;

use Andriichuk\KeepEnv\Validation\EqualsValidator;
use Generator;
use PHPUnit\Framework\TestCase;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class EqualsValidatorTest extends TestCase
{
    /**
     * @dataProvider validationCasesProvider
     *
     * @param mixed $subject
     * @param mixed $equals
     */
    public function testValidationCases($subject, $equals, bool $expectedResult, string $expectedMessage, string $message): void
    {
        $validator = new EqualsValidator();
        $result = $validator->validate($subject, [$equals]);

        $this->assertEquals($expectedResult, $result, $message);

        if (!$result) {
            $this->assertEquals($expectedMessage, $validator->message(['equals' => $equals]));
        }
    }

    public function validationCasesProvider(): Generator
    {
        yield [
            'subject' => 'local',
            'equals' => 'local',
            'expected_result' => true,
            'expected_message' => '',
            'message' => 'Regular string',
        ];

        yield [
            'subject' => '0',
            'equals' => '0',
            'expected_result' => true,
            'expected_message' => '',
            'message' => 'String with zero',
        ];

        yield [
            'subject' => '',
            'equals' => '',
            'expected_result' => true,
            'expected_message' => '',
            'message' => 'Empty strings',
        ];

        yield [
            'subject' => null,
            'equals' => null,
            'expected_result' => true,
            'expected_message' => '',
            'message' => 'Nullable values',
        ];

        yield [
            'subject' => '',
            'equals' => ' ',
            'expected_result' => false,
            'expected_message' => 'The value must be equal to ` `.',
            'message' => 'Empty string with space',
        ];

        yield [
            'subject' => 0,
            'equals' => '0',
            'expected_result' => false,
            'expected_message' => 'The value must be equal to `0`.',
            'message' => 'Strict type zero',
        ];

        yield [
            'subject' => 'production',
            'equals' => 'local',
            'expected_result' => false,
            'expected_message' => 'The value must be equal to `local`.',
            'message' => 'Different strings',
        ];
    }
}
