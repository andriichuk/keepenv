<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Tests\Functional\Environment\Loader;

use Andriichuk\KeepEnv\Environment\Loader\SymfonyDotEnvStateLoader;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamFile;
use PHPUnit\Framework\TestCase;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class SymfonyDotEnvStateLoaderTest extends TestCase
{
    private vfsStreamDirectory $rootFolder;
    private SymfonyDotEnvStateLoader $loader;

    protected function setUp(): void
    {
        $this->rootFolder = vfsStream::setup('src');
        $this->rootFolder->addChild(
            (new vfsStreamFile('.env'))
                ->setContent("SECOND_APP_ENV=production\nSECOND_APP_DEBUG=true"),
        );

        $this->loader = new SymfonyDotEnvStateLoader();

        $_ENV['SECOND_APP_ENV'] = 'dev';
        $_ENV['SECOND_APP_RANDOM_KEY'] = 'test_123';
    }

    protected function tearDown(): void
    {
        $_ENV = $_SERVER = [];
    }

    public function testLoaderCanProvideVariablesWithoutOverriding(): void
    {
        $variables = $this->loader->load([$this->rootFolder->getChild('.env')->url()], false);

        $this->assertArrayHasKey('SECOND_APP_ENV', $variables);
        $this->assertEquals('dev', $variables['SECOND_APP_ENV']);

        $this->assertArrayHasKey('SECOND_APP_RANDOM_KEY', $variables);
        $this->assertEquals('test_123', $variables['SECOND_APP_RANDOM_KEY']);

        $this->assertArrayHasKey('SECOND_APP_DEBUG', $variables);
        $this->assertEquals('true', $variables['SECOND_APP_DEBUG']);
    }

    public function testLoaderCanProvideVariablesWithOverriding(): void
    {
        $variables = $this->loader->load([$this->rootFolder->getChild('.env')->url()], true);

        $this->assertArrayHasKey('SECOND_APP_ENV', $variables);
        $this->assertEquals('production', $variables['SECOND_APP_ENV']);

        $this->assertArrayHasKey('SECOND_APP_RANDOM_KEY', $variables);
        $this->assertEquals('test_123', $variables['SECOND_APP_RANDOM_KEY']);

        $this->assertArrayHasKey('SECOND_APP_DEBUG', $variables);
        $this->assertEquals('true', $variables['SECOND_APP_DEBUG']);
    }
}
