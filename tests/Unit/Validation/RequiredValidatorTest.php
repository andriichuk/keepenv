<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Unit\Validation;

use Andriichuk\KeepEnv\Validation\RequiredValidator;
use Generator;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class RequiredValidatorTest extends TestCase
{
    /**
     * @dataProvider validationCasesProvider
     *
     * @param string|bool $required
     */
    public function testValidationCases(?string $subject, $required, bool $expectedResult, string $message): void
    {
        $validator = new RequiredValidator();
        $result = $validator->validate($subject, [$required]);

        $this->assertEquals($expectedResult, $result, $message);
    }

    public function validationCasesProvider(): Generator
    {
        yield [
            'subject' => '0',
            'required' => true,
            'expected_result' => true,
            'message' => 'Valid numeric value',
        ];

        yield [
            'subject' => 'null',
            'required' => true,
            'expected_result' => true,
            'message' => 'Valid null string',
        ];

        yield [
            'subject' => '',
            'required' => 'false',
            'expected_result' => true,
            'message' => 'Not required empty string with string option',
        ];

        yield [
            'subject' => '292732837',
            'required' => true,
            'expected_result' => true,
            'message' => 'Required normal string',
        ];

        yield [
            'subject' => '',
            'required' => 'true',
            'expected_result' => false,
            'message' => 'Required empty string with string option',
        ];

        yield [
            'subject' => null,
            'required' => true,
            'expected_result' => false,
            'message' => 'Required empty string with string option',
        ];

        yield [
            'subject' => '  ',
            'required' => true,
            'expected_result' => false,
            'message' => 'Required string with spaces',
        ];
    }

    public function testValidatorThrowExceptionOnInvalidOption(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $validator = new RequiredValidator();
        $validator->validate('123', ['yes']);
    }
}
