<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Unit\Validation;

use Andriichuk\KeepEnv\Validation\EnumValidator;
use Generator;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class EnumValidatorTest extends TestCase
{
    /**
     * @dataProvider validationCasesProvider
     *
     * @param mixed $subject
     */
    public function testValidationCases($subject, array $cases, bool $expectedResult, string $expectedMessage, string $message): void
    {
        $validator = new EnumValidator();
        $result = $validator->validate($subject, $cases);

        $this->assertEquals($expectedResult, $result, $message);

        if (!$result) {
            $this->assertEquals($expectedMessage, $validator->message(['cases' => $cases]));
        }
    }

    public function validationCasesProvider(): Generator
    {
        yield [
            'subject' => 'yes',
            'cases' => ['yes', 'no'],
            'expected_result' => true,
            'expected_message' => '',
            'message' => 'Boolean strings',
        ];

        yield [
            'subject' => true,
            'cases' => [true, false],
            'expected_result' => true,
            'expected_message' => '',
            'message' => 'Boolean values',
        ];

        yield [
            'subject' => '1234',
            'cases' => ['1234', '4321'],
            'expected_result' => true,
            'expected_message' => '',
            'message' => 'Numeric strings',
        ];

        yield [
            'subject' => '',
            'cases' => ['local', 'production'],
            'expected_result' => false,
            'expected_message' => 'The value must match one of the values: local, production.',
            'message' => 'Empty string',
        ];

        yield [
            'subject' => null,
            'cases' => ['local', 'production'],
            'expected_result' => false,
            'expected_message' => 'The value must match one of the values: local, production.',
            'message' => 'Empty string',
        ];

        yield [
            'subject' => false,
            'cases' => ['false', 'true'],
            'expected_result' => false,
            'expected_message' => 'The value must match one of the values: false, true.',
            'message' => 'Different types of boolean',
        ];
    }

    public function testValidatorThrowsExceptionOnInvalidOption(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $validator = new EnumValidator();
        $validator->validate('local', []);
    }
}
