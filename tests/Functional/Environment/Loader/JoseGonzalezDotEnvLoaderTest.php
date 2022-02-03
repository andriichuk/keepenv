<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Tests\Functional\Environment\Loader;

use Andriichuk\KeepEnv\Environment\Loader\JoseGonzalezDotEnvStateLoader;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamFile;
use PHPUnit\Framework\TestCase;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class JoseGonzalezDotEnvLoaderTest extends TestCase
{
    private vfsStreamDirectory $rootFolder;
    private JoseGonzalezDotEnvStateLoader $loader;

    protected function setUp(): void
    {
        $this->rootFolder = vfsStream::setup('src');
        $this->rootFolder->addChild(
            (new vfsStreamFile('.env'))
                ->setContent("THIRD_APP_ENV=production\nTHIRD_APP_DEBUG=true"),
        );

        $this->loader = new JoseGonzalezDotEnvStateLoader();

        $_ENV['THIRD_APP_ENV'] = 'dev';
        $_ENV['THIRD_APP_RANDOM_KEY'] = 'test_123';
        $_ENV['THIRD_APP_NEW_NUMERIC_KEY'] = 123;
        $_ENV['THIRD_APP_NEW_BOOL_KEY'] = false;
    }

    protected function tearDown(): void
    {
        $_ENV = $_SERVER = [];
    }

    public function testLoaderCanProvideVariablesWithoutOverriding(): void
    {
        $variables = $this->loader->load([$this->rootFolder->getChild('.env')->url()], false);

        $this->assertArrayHasKey('THIRD_APP_ENV', $variables);
        $this->assertEquals('dev', $variables['THIRD_APP_ENV']);

        $this->assertArrayHasKey('THIRD_APP_DEBUG', $variables);
        $this->assertEquals(true, $variables['THIRD_APP_DEBUG']);

        $this->assertArrayHasKey('THIRD_APP_NEW_NUMERIC_KEY', $variables);
        $this->assertEquals(123, $variables['THIRD_APP_NEW_NUMERIC_KEY']);

        $this->assertArrayHasKey('THIRD_APP_NEW_BOOL_KEY', $variables);
        $this->assertEquals(false, $variables['THIRD_APP_NEW_BOOL_KEY']);
    }

    public function testLoaderCanProvideVariablesWithOverriding(): void
    {
        $variables = $this->loader->load([$this->rootFolder->getChild('.env')->url()], true);

        $this->assertArrayHasKey('THIRD_APP_ENV', $variables);
        $this->assertEquals('production', $variables['THIRD_APP_ENV']);

        $this->assertArrayHasKey('THIRD_APP_DEBUG', $variables);
        $this->assertEquals(true, $variables['THIRD_APP_DEBUG']);

        $this->assertArrayHasKey('THIRD_APP_RANDOM_KEY', $variables);
        $this->assertEquals('test_123', $variables['THIRD_APP_RANDOM_KEY']);
    }
}
