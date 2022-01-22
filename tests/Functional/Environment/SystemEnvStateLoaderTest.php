<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Functional\Environment;

use Andriichuk\KeepEnv\Environment\Loader\SystemEnvStateLoader;
use PHPUnit\Framework\TestCase;

class SystemEnvStateLoaderTest extends TestCase
{
    protected function setUp(): void
    {
        $_ENV['API_TEST_KEY'] = 'test12345';
    }

    protected function tearDown(): void
    {
        unset($_ENV['API_TEST_KEY']);
    }

    public function testSystemLoaderCanProvideVariables(): void
    {
        $loader = new SystemEnvStateLoader();
        $variables = $loader->load([], false);

        $this->assertArrayHasKey('API_TEST_KEY', $variables);
        $this->assertEquals('test12345', $variables['API_TEST_KEY']);
    }
}
