<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Tests\Functional\Environment\Reader;

use Andriichuk\KeepEnv\Environment\Reader\VlucasPhpDotEnvFileReader;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamFile;
use PHPUnit\Framework\TestCase;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class VlucasPhpDotEnvFileReaderTest extends TestCase
{
    private vfsStreamDirectory $rootFolder;
    private VlucasPhpDotEnvFileReader $reader;

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

        $this->reader = new VlucasPhpDotEnvFileReader();

        // Check that environment variables cannot affect the file reading
        $_ENV['APP_ENV'] = 'dev';
        $_ENV['APP_RANDOM_KEY'] = 'test_123';
    }

    protected function tearDown(): void
    {
        unset($_ENV['APP_ENV'], $_ENV['APP_RANDOM_KEY']);
    }

    public function testReaderCanProvideVariablesFromFile(): void
    {
        $variables = $this->reader->read(dirname($this->rootFolder->getChild('.env')->url()));

        $this->assertEquals(
            [
                'APP_NAME' => 'KeepEnv',
                'APP_ENV' => 'production',
                'APP_DEBUG' => 'true',
                'APP_KEY' => '',
                'REDIS_PORT' => '6379',
                'MAIL_FROM_ADDRESS' => 'test@test.com',
                'PAYMENT_FEATURE_ENABLED' => 'false',
                'REDIS_PASSWORD' => 'null',
                'PROFILING_LIMIT' => '2.50',
                'DEBUG_IP' => '127.0.0.1',
                'MAINTENANCE' => 'on',
            ],
            $variables,
        );
    }
}
