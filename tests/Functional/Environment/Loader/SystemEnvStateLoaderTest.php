<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Tests\Functional\Environment\Loader;

use Andriichuk\KeepEnv\Environment\Loader\SystemEnvStateLoader;
use PHPUnit\Framework\TestCase;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class SystemEnvStateLoaderTest extends TestCase
{
    protected function setUp(): void
    {
        $_ENV['API_TEST_KEY'] = 'test12345';
    }

    protected function tearDown(): void
    {
        $_ENV = $_SERVER = [];
    }

    public function testSystemLoaderCanProvideVariables(): void
    {
        $loader = new SystemEnvStateLoader();
        $variables = $loader->load([], false);

        $this->assertArrayHasKey('API_TEST_KEY', $variables);
        $this->assertEquals('test12345', $variables['API_TEST_KEY']);
    }
}
