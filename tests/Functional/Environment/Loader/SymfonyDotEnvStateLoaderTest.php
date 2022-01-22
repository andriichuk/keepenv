<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Functional\Environment\Loader;

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
                ->setContent(
                    file_get_contents(
                        dirname(__DIR__, 3) . '/fixtures/common/.env',
                    ),
                ),
        );

        $this->loader = new SymfonyDotEnvStateLoader();

        $_ENV['APP_ENV'] = 'dev';
        $_ENV['APP_RANDOM_KEY'] = 'test_123';
    }

    protected function tearDown(): void
    {
        unset($_ENV['APP_ENV'], $_ENV['APP_RANDOM_KEY']);
    }

    public function testLoaderCanProvideVariablesWithoutOverriding(): void
    {
        $variables = $this->loader->load([$this->rootFolder->getChild('.env')->url()], false);

        $this->assertArrayHasKey('APP_ENV', $variables);
        $this->assertEquals('dev', $variables['APP_ENV']);

        $this->assertArrayHasKey('APP_DEBUG', $variables);
        $this->assertEquals('true', $variables['APP_DEBUG']);
    }

    public function testLoaderCanProvideVariablesWithOverriding(): void
    {
        $variables = $this->loader->load([$this->rootFolder->getChild('.env')->url()], true);

        $this->assertArrayHasKey('APP_ENV', $variables);
        $this->assertEquals('production', $variables['APP_ENV']);

        $this->assertArrayHasKey('APP_KEY', $variables);
        $this->assertEquals('', $variables['APP_KEY']);

        $this->assertArrayHasKey('APP_RANDOM_KEY', $variables);
        $this->assertEquals('test_123', $variables['APP_RANDOM_KEY']);
    }
}