<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Tests\Unit\Validation\Rules;

use Andriichuk\KeepEnv\Validation\Rules\RuleInterface;
use Andriichuk\KeepEnv\Validation\Rules\RulesRegistry;
use OutOfRangeException;
use PHPUnit\Framework\TestCase;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class RulesRegistryTest extends TestCase
{
    public function testRegistryCanStoreNewValidator(): void
    {
        $registry = new RulesRegistry();

        $validator = $this->createConfiguredMock(RuleInterface::class, [
            'alias' => 'email_test',
        ]);
        $registry->add($validator);

        $this->assertEquals($validator, $registry->get('email_test'));
    }

    public function testRegistryThrowsExceptionOnRetrieveMissingValidator(): void
    {
        $this->expectException(OutOfRangeException::class);
        $registry = new RulesRegistry();

        $validator = $this->createConfiguredMock(RuleInterface::class, [
            'alias' => 'email_test',
        ]);
        $registry->add($validator);

        $registry->get('email_check');
    }

    public function testRegistryCanProvideListOfRuleAliases(): void
    {
        $registry = new RulesRegistry();

        $registry->add(
            $this->createConfiguredMock(RuleInterface::class, [
                'alias' => 'email',
            ])
        );

        $registry->add(
            $this->createConfiguredMock(RuleInterface::class, [
                'alias' => 'numeric',
            ])
        );

        $this->assertEquals(
            ['email'],
            $registry->listOfAliases(['numeric']),
        );
    }
}
