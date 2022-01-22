<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Unit\Environment;

use Andriichuk\KeepEnv\Environment\Reader\EnvReaderFactory;
use Andriichuk\KeepEnv\Environment\Reader\SymfonyDotEnvFileReader;
use Andriichuk\KeepEnv\Environment\Reader\VlucasPhpDotEnvFileReader;
use Generator;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class EnvReaderFactoryTest extends TestCase
{
    private EnvReaderFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new EnvReaderFactory();
    }

    /**
     * @dataProvider readersProvider
     */
    public function testFactoryCanMakeReader(string $type, string $instanceOf, string $message): void
    {
        $reader = $this->factory->make($type);

        $this->assertInstanceOf($instanceOf, $reader, $message);
    }

    public function readersProvider(): Generator
    {
        yield [
            'type' => 'auto',
            'instance_of' => VlucasPhpDotEnvFileReader::class,
            'message' => 'Reader based on library availability',
        ];

        yield [
            'type' => 'vlucas/phpdotenv',
            'instance_of' => VlucasPhpDotEnvFileReader::class,
            'message' => 'Reader based on `vlucas/phpdotenv`',
        ];

        yield [
            'type' => 'symfony/dotenv',
            'instance_of' => SymfonyDotEnvFileReader::class,
            'message' => 'Reader based on `symfony/dotenv`',
        ];
    }

    public function testReaderThrowsExceptionForUndefinedType(): void
    {
        $this->expectException(RuntimeException::class);

        $this->factory->make('not_exists');
    }
}
