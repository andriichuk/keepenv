<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Tests\Functional\Environment\Loader;

use Andriichuk\KeepEnv\Environment\Loader\VlucasPhpDotEnvStateLoader;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamFile;
use PHPUnit\Framework\TestCase;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class VlucasPhpDotEnvStateLoaderTest extends TestCase
{
    private vfsStreamDirectory $rootFolder;
    private VlucasPhpDotEnvStateLoader $loader;

    protected function setUp(): void
    {
        $this->rootFolder = vfsStream::setup('src');
        $this->rootFolder->addChild(
            (new vfsStreamFile('.env'))
                ->setContent("FIRST_APP_ENV=production\nFIRST_APP_DEBUG=true"),
        );

        $this->loader = new VlucasPhpDotEnvStateLoader();

        $_ENV['FIRST_APP_ENV'] = 'dev';
        $_ENV['FIRST_APP_RANDOM_KEY'] = 'test_123';
    }

    protected function tearDown(): void
    {
        $_ENV = $_SERVER = [];
    }

    public function testLoaderCanProvideVariablesWithoutOverriding(): void
    {
        $variables = $this->loader->load([dirname($this->rootFolder->getChild('.env')->url())], false);

        $this->assertArrayHasKey('FIRST_APP_ENV', $variables);
        $this->assertEquals('dev', $variables['FIRST_APP_ENV']);

        $this->assertArrayHasKey('FIRST_APP_RANDOM_KEY', $variables);
        $this->assertEquals('test_123', $variables['FIRST_APP_RANDOM_KEY']);

        $this->assertArrayHasKey('FIRST_APP_DEBUG', $variables);
        $this->assertEquals('true', $variables['FIRST_APP_DEBUG']);
    }

    public function testLoaderCanProvideVariablesWithOverriding(): void
    {
        $variables = $this->loader->load([dirname($this->rootFolder->getChild('.env')->url())], true);

        $this->assertArrayHasKey('FIRST_APP_ENV', $variables);
        $this->assertEquals('production', $variables['FIRST_APP_ENV']);

        $this->assertArrayHasKey('FIRST_APP_RANDOM_KEY', $variables);
        $this->assertEquals('test_123', $variables['FIRST_APP_RANDOM_KEY']);

        $this->assertArrayHasKey('FIRST_APP_DEBUG', $variables);
        $this->assertEquals('true', $variables['FIRST_APP_DEBUG']);
    }
}
