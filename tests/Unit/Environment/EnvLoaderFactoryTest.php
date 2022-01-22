<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Unit\Environment;

use Andriichuk\KeepEnv\Environment\Loader\EnvLoaderFactory;
use Andriichuk\KeepEnv\Environment\Loader\SystemEnvStateLoader;
use Andriichuk\KeepEnv\Environment\Loader\VlucasPhpDotEnvStateLoader;
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

    public function testFactoryCanMakeSystemLoader(): void
    {
        $loader = $this->factory->make('system');

        $this->assertInstanceOf(SystemEnvStateLoader::class, $loader);
    }

    public function testFactoryCanMakeInstalledLoader(): void
    {
        $loader = $this->factory->make('auto');

        $this->assertInstanceOf(VlucasPhpDotEnvStateLoader::class, $loader);
    }

    public function testFactoryThrowsExceptionOnWrongType(): void
    {
        $this->expectException(RuntimeException::class);
        $this->factory->make('wrong');
    }
}
