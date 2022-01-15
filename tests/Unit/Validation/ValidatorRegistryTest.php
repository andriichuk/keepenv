<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Unit\Validation;

use Andriichuk\KeepEnv\Validation\ValidatorInterface;
use Andriichuk\KeepEnv\Validation\ValidatorRegistry;
use OutOfRangeException;
use PHPUnit\Framework\TestCase;

class ValidatorRegistryTest extends TestCase
{
    public function testRegistryCanStoreNewValidator(): void
    {
        $registry = new ValidatorRegistry();

        $validator = $this->createConfiguredMock(ValidatorInterface::class, [
            'alias' => 'email_test',
        ]);
        $registry->add($validator);

        $this->assertEquals($validator, $registry->get('email_test'));
    }

    public function testRegistryThrowsExceptionOnRetrieveMissingValidator(): void
    {
        $this->expectException(OutOfRangeException::class);
        $registry = new ValidatorRegistry();

        $validator = $this->createConfiguredMock(ValidatorInterface::class, [
            'alias' => 'email_test',
        ]);
        $registry->add($validator);

        $registry->get('email_check');
    }
}
