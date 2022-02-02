<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Tests\Functional\Dump;

use Andriichuk\KeepEnv\Dump\DumpService;
use Andriichuk\KeepEnv\Dump\Exceptions\EnvFileAlreadyExistsException;
use Andriichuk\KeepEnv\Environment\Loader\SystemEnvStateLoader;
use Andriichuk\KeepEnv\Environment\Loader\VlucasPhpDotEnvStateLoader;
use Andriichuk\KeepEnv\Environment\Writer\EnvFileManager;
use Andriichuk\KeepEnv\Environment\Writer\EnvFileWriter;
use Andriichuk\KeepEnv\Specification\Reader\SpecYamlReader;
use Andriichuk\KeepEnv\Specification\SpecificationArrayBuilder;
use Andriichuk\KeepEnv\Utils\Stringify;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamFile;
use PHPUnit\Framework\TestCase;

class DumpServiceTest extends TestCase
{
    private vfsStreamDirectory $rootFolder;

    protected function setUp(): void
    {
        $this->rootFolder = vfsStream::setup('src');

        $this->rootFolder->addChild(
            (new vfsStreamFile('keepenv.yaml'))
                ->setContent(
                    file_get_contents(dirname(__DIR__, 2) . '/fixtures/case_8/keepenv.yaml'),
                ),
        );
    }

    protected function tearDown(): void
    {
        $_ENV = $_SERVER = [];
    }

    public function testServiceCanDumpExistingVariables(): void
    {
        $this->rootFolder->addChild(
            (new vfsStreamFile('.env'))
                ->setContent(
                    file_get_contents(dirname(__DIR__, 2) . '/fixtures/case_8/.env'),
                ),
        );

        $fileManager = new EnvFileManager('vfs://src/.env_test_1');

        $service = new DumpService(
            new SpecYamlReader(new SpecificationArrayBuilder()),
            new VlucasPhpDotEnvStateLoader(),
            $fileManager,
            new EnvFileWriter($fileManager),
            new Stringify(),
        );

        $service->dump(
            'common',
            [dirname($this->rootFolder->getChild('.env')->url())],
            $this->rootFolder->getChild('keepenv.yaml')->url(),
            true,
            false,
        );

        $this->assertFileEquals(
            dirname(__DIR__, 2) . '/fixtures/case_8/.env.common',
            $this->rootFolder->getChild('.env_test_1')->url(),
        );
    }

    public function testServiceCanDumpSystemVariables(): void
    {
        $fileManager = new EnvFileManager('vfs://src/.env_test_2');

        $service = new DumpService(
            new SpecYamlReader(new SpecificationArrayBuilder()),
            new SystemEnvStateLoader(),
            $fileManager,
            new EnvFileWriter($fileManager),
            new Stringify(),
        );

        $_ENV['APP_ENV'] = 'testing';
        $_ENV['APP_DEBUG'] = true;
        $_ENV['APP_TEST_NEW_1'] = null;
        $_ENV['APP_LOCALE'] = null;

        $service->dump(
            'common',
            [],
            $this->rootFolder->getChild('keepenv.yaml')->url(),
            true,
            false,
        );

        $this->assertFileEquals(
            dirname(__DIR__, 2) . '/fixtures/case_8/.env.system',
            $this->rootFolder->getChild('.env_test_2')->url(),
        );
    }

    public function testServiceCanThrowExceptionOnAttemptToOverrideExistingEnvFile(): void
    {
        $this->expectException(EnvFileAlreadyExistsException::class);

        $this->rootFolder->addChild((new vfsStreamFile('.env'))->setContent(''));
        $fileManager = new EnvFileManager('vfs://src/.env');

        $service = new DumpService(
            new SpecYamlReader(new SpecificationArrayBuilder()),
            new VlucasPhpDotEnvStateLoader(),
            $fileManager,
            new EnvFileWriter($fileManager),
            new Stringify(),
        );

        $service->dump(
            'common',
            [dirname($this->rootFolder->getChild('.env')->url())],
            $this->rootFolder->getChild('keepenv.yaml')->url(),
            true,
            false,
        );
    }
}
