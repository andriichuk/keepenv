<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Tests\Unit\Validation\Rules;

use Andriichuk\KeepEnv\Validation\Rules\Exceptions\RuleOptionsException;
use Andriichuk\KeepEnv\Validation\Rules\RequiredRule;
use Generator;
use PHPUnit\Framework\TestCase;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class RequiredRuleTest extends TestCase
{
    /**
     * @dataProvider validationCasesProvider
     *
     * @param string|bool $required
     */
    public function testValidationCases(?string $subject, $required, bool $expectedResult, string $message): void
    {
        $validator = new RequiredRule();
        $result = $validator->validate($subject, $required);

        $this->assertEquals($expectedResult, $result, $message);

        if (!$result) {
            $this->assertEquals('The value is required.', $validator->message([]));
        }
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
            'required' => false,
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
            'required' => true,
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
        $this->expectException(RuleOptionsException::class);

        $validator = new \Andriichuk\KeepEnv\Validation\Rules\RequiredRule();
        $validator->validate('123', ['yes']);
    }
}
