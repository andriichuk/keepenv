<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Tests\Functional\Environment\Reader;

use Andriichuk\KeepEnv\Environment\Reader\JoseGonzalezDotEnvFileReader;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamFile;
use PHPUnit\Framework\TestCase;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class JoseGonzalezDotEnvFileReaderTest extends TestCase
{
    private vfsStreamDirectory $rootFolder;
    private JoseGonzalezDotEnvFileReader $reader;

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

        $this->reader = new JoseGonzalezDotEnvFileReader();

        $_ENV['APP_ENV'] = 'dev';
        $_ENV['APP_RANDOM_KEY'] = 'test_123';
    }

    protected function tearDown(): void
    {
        unset($_ENV['APP_ENV'], $_ENV['APP_RANDOM_KEY']);
    }

    public function testReaderCanProvideVariablesFromFile(): void
    {
        $variables = $this->reader->read($this->rootFolder->getChild('.env')->url());

        $this->assertEquals(
            [
                'APP_NAME' => 'KeepEnv',
                'APP_ENV' => 'production',
                'APP_DEBUG' => true,
                'PAYMENT_FEATURE_ENABLED' => 'false',
                'APP_KEY' => null,
                'REDIS_PORT' => 6379,
                'REDIS_PASSWORD' => null,
                'PROFILING_LIMIT' => 2.5,
                'MAIL_FROM_ADDRESS' => 'test@test.com',
                'DEBUG_IP' => '127.0.0.1',
                'MAINTENANCE' => 'on',
            ],
            $variables,
        );
    }
}
