<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Unit\Validation;

use Andriichuk\KeepEnv\Validation\EmailValidator;
use Generator;
use PHPUnit\Framework\TestCase;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class EmailValidatorTest extends TestCase
{
    /**
     * @dataProvider validationCasesProvider
     */
    public function testValidationCases(string $subject, bool $expectedResult, string $message): void
    {
        $validator = new EmailValidator();
        $result = $validator->validate($subject, []);

        $this->assertEquals($expectedResult, $result, $message);
    }

    public function validationCasesProvider(): Generator
    {
        yield [
            'subject' => 'test@test.com',
            'expected_result' => true,
            'message' => 'Valid email',
        ];

        yield [
            'subject' => '_______@test.com',
            'expected_result' => true,
            'message' => 'Complex valid email 1',
        ];

        yield [
            'subject' => 'email@[123.123.123.123]',
            'expected_result' => true,
            'message' => 'Complex valid email 2',
        ];

        yield [
            'subject' => 'much."more\ unusual"@example.com',
            'expected_result' => true,
            'message' => 'Complex valid email 3',
        ];

        yield [
            'subject' => '',
            'expected_result' => false,
            'message' => 'Invalid email 1',
        ];

        yield [
            'subject' => 'test@',
            'expected_result' => false,
            'message' => 'Invalid email 2',
        ];

        yield [
            'subject' => '”(),:;<>[\]@example.com',
            'expected_result' => false,
            'message' => 'Invalid email 3',
        ];
    }
}
