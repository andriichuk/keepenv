<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Tests\Unit\Environment;

use Andriichuk\KeepEnv\Environment\Loader\EnvLoaderFactory;
use Andriichuk\KeepEnv\Environment\Loader\JoseGonzalezDotEnvStateLoader;
use Andriichuk\KeepEnv\Environment\Loader\SymfonyDotEnvStateLoader;
use Andriichuk\KeepEnv\Environment\Loader\SystemEnvStateLoader;
use Andriichuk\KeepEnv\Environment\Loader\VlucasPhpDotEnvStateLoader;
use Generator;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class EnvLoaderFactoryTest extends TestCase
{
    private EnvLoaderFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new EnvLoaderFactory();
    }

    /**
     * @dataProvider loadersProvider
     */
    public function testFactoryCanMakeLoader(string $type, string $instanceOf, string $message): void
    {
        $reader = $this->factory->make($type);

        $this->assertInstanceOf($instanceOf, $reader, $message);
    }

    public function loadersProvider(): Generator
    {
        yield [
            'type' => 'system',
            'instance_of' => SystemEnvStateLoader::class,
            'message' => 'Reader based on library availability',
        ];

        yield [
            'type' => 'auto',
            'instance_of' => VlucasPhpDotEnvStateLoader::class,
            'message' => 'Reader based on library availability',
        ];

        yield [
            'type' => 'vlucas/phpdotenv',
            'instance_of' => VlucasPhpDotEnvStateLoader::class,
            'message' => 'Reader based on `vlucas/phpdotenv`',
        ];

        yield [
            'type' => 'symfony/dotenv',
            'instance_of' => SymfonyDotEnvStateLoader::class,
            'message' => 'Reader based on `symfony/dotenv`',
        ];

        yield [
            'type' => 'josegonzalez/dotenv',
            'instance_of' => JoseGonzalezDotEnvStateLoader::class,
            'message' => 'Reader based on `josegonzalez/dotenv`',
        ];
    }

    public function testFactoryThrowsExceptionOnWrongType(): void
    {
        $this->expectException(RuntimeException::class);
        $this->factory->make('wrong');
    }
}
