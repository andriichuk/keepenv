<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Tests\Unit\Validation\Rules;

use Andriichuk\KeepEnv\Validation\Rules\IpRule;
use Generator;
use PHPUnit\Framework\TestCase;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class IpRuleTest extends TestCase
{
    /**
     * @dataProvider validationCasesProvider
     */
    public function testValidationCases(string $subject, bool $expectedResult, string $message): void
    {
        $validator = new IpRule();
        $result = $validator->validate($subject, true);

        $this->assertEquals($expectedResult, $result, $message);

        if (!$result) {
            $this->assertEquals('The value must be a valid IP address.', $validator->message([]));
        }
    }

    public function validationCasesProvider(): Generator
    {
        yield [
            'subject' => '127.0.0.1',
            'expected_result' => true,
            'message' => 'Valid local IP address',
        ];

        yield [
            'subject' => '22.22',
            'expected_result' => false,
            'message' => 'Invalid IP address',
        ];
    }
}
