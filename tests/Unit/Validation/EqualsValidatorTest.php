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
    public function testValidationCases($subject, $equals, bool $expectedResult, string $message): void
    {
        $validator = new EqualsValidator();
        $result = $validator->validate($subject, [$equals]);

        $this->assertEquals($expectedResult, $result, $message);
    }

    public function validationCasesProvider(): Generator
    {
        yield [
            'subject' => 'local',
            'equals' => 'local',
            'expected_result' => true,
            'message' => 'Regular string',
        ];

        yield [
            'subject' => '0',
            'equals' => '0',
            'expected_result' => true,
            'message' => 'String with zero',
        ];

        yield [
            'subject' => '',
            'equals' => '',
            'expected_result' => true,
            'message' => 'Empty strings',
        ];

        yield [
            'subject' => null,
            'equals' => null,
            'expected_result' => true,
            'message' => 'Nullable values',
        ];

        yield [
            'subject' => '',
            'equals' => ' ',
            'expected_result' => false,
            'message' => 'Empty string with space',
        ];

        yield [
            'subject' => 0,
            'equals' => '0',
            'expected_result' => false,
            'message' => 'Strict type zero',
        ];
    }
}
