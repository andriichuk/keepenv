<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Tests\Functional\Generator;

use Andriichuk\KeepEnv\Environment\Reader\EnvReaderFactory;
use Andriichuk\KeepEnv\Environment\Reader\EnvReaderType;
use Andriichuk\KeepEnv\Generator\Exceptions\SpecGeneratorException;
use Andriichuk\KeepEnv\Generator\Presets\PresetFactory;
use Andriichuk\KeepEnv\Generator\SpecGenerator;
use Andriichuk\KeepEnv\Specification\Writer\SpecWriterFactory;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamFile;
use PHPUnit\Framework\TestCase;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class SpecGeneratorTest extends TestCase
{
    private vfsStreamDirectory $rootFolder;

    protected function setUp(): void
    {
        $this->rootFolder = vfsStream::setup('src');
    }

    protected function tearDown(): void
    {
        $_ENV = [];
    }

    public function testGeneratorCanAutomaticallyDetectReaderBasedOnAvailablePackage(): void
    {
        $this->rootFolder->addChild(
            (new vfsStreamFile('.env'))
                ->setContent(
                    file_get_contents(
                        dirname(__DIR__, 2) . '/fixtures/common/.env',
                    ),
                ),
        );

        $specGenerator = new SpecGenerator(
            (new EnvReaderFactory())->make(EnvReaderType::AUTO),
            (new SpecWriterFactory())->basedOnResource('vfs://src/keepenv.yaml'),
            new PresetFactory(),
        );

        $specGenerator->generate(
            'common',
            [dirname($this->rootFolder->getChild('.env')->url())],
            'vfs://src/keepenv.yaml',
            static fn (): bool => true,
        );

        $this->assertFileEquals(
            dirname(__DIR__, 2) . '/fixtures/common/keepenv.yaml',
            $this->rootFolder->getChild('keepenv.yaml')->url(),
        );
    }

    public function testGeneratorCanCreateSpecUsingLaravelPreset(): void
    {
        $this->rootFolder->addChild(
            (new vfsStreamFile('.env'))
                ->setContent(
                    file_get_contents(
                        dirname(__DIR__, 2) . '/fixtures/generator/laravel_preset/.env',
                    ),
                ),
        );

        $specGenerator = new SpecGenerator(
            (new EnvReaderFactory())->make(EnvReaderType::AUTO),
            (new SpecWriterFactory())->basedOnResource('vfs://src/keepenv_laravel.yaml'),
            new PresetFactory(),
        );

        $specGenerator->generate(
            'common',
            [dirname($this->rootFolder->getChild('.env')->url())],
            'vfs://src/keepenv.yaml',
            static fn () => true,
            'laravel'
        );

        $this->assertFileEquals(
            dirname(__DIR__, 2) . '/fixtures/generator/laravel_preset/keepenv.yaml',
            $this->rootFolder->getChild('keepenv.yaml')->url(),
        );
    }

    public function testGeneratorCanCreateSpecUsingSymfonyPreset(): void
    {
        $this->rootFolder->addChild(
            (new vfsStreamFile('.env'))
                ->setContent(
                    file_get_contents(
                        dirname(__DIR__, 2) . '/fixtures/generator/symfony_preset/.env',
                    ),
                ),
        );

        $specGenerator = new SpecGenerator(
            (new EnvReaderFactory())->make(EnvReaderType::SYMFONY),
            (new SpecWriterFactory())->basedOnResource('vfs://src/keepenv.yaml'),
            new PresetFactory(),
        );

        $specGenerator->generate(
            'common',
            [$this->rootFolder->getChild('.env')->url()],
            'vfs://src/keepenv.yaml',
            static fn () => true,
            'symfony'
        );

        $this->assertFileEquals(
            dirname(__DIR__, 2) . '/fixtures/generator/symfony_preset/keepenv.yaml',
            $this->rootFolder->getChild('keepenv.yaml')->url(),
        );
    }

    public function testGeneratorCanCreateSpecUsingSystemVariables(): void
    {
        $_ENV['APP_NAME'] = 'KeepEnv';
        $_ENV['APP_ENV'] = 'production';
        $_ENV['APP_DEBUG'] = true;
        $_ENV['APP_KEY'] = '';
        $_ENV['REDIS_PORT'] = 6379;
        $_ENV['REDIS_PASSWORD'] = null;
        $_ENV['MAIL_FROM_ADDRESS'] = 'test@test.com';

        $specGenerator = new SpecGenerator(
            (new EnvReaderFactory())->make(EnvReaderType::SYSTEM),
            (new SpecWriterFactory())->basedOnResource('vfs://src/keepenv.yaml'),
            new PresetFactory(),
        );

        $specGenerator->generate(
            'common',
            [],
            'vfs://src/keepenv.yaml',
            static fn (): bool => true,
        );

        $this->assertFileEquals(
            dirname(__DIR__, 2) . '/fixtures/common/keepenv.yaml',
            $this->rootFolder->getChild('keepenv.yaml')->url(),
        );
    }

    public function testGeneratorThrowsExceptionOnAlreadyExistingSpecFile(): void
    {
        $this->expectException(SpecGeneratorException::class);

        $this->rootFolder->addChild((new vfsStreamFile('.env'))->setContent(''));
        $this->rootFolder->addChild((new vfsStreamFile('keepenv.yaml'))->setContent(''));

        $specGenerator = new SpecGenerator(
            (new EnvReaderFactory())->make(EnvReaderType::AUTO),
            (new SpecWriterFactory())->basedOnResource('vfs://src/keepenv.yaml'),
            new PresetFactory(),
        );

        $specGenerator->generate(
            'common',
            [dirname($this->rootFolder->getChild('.env')->url())],
            'vfs://src/keepenv.yaml',
            static fn () => false,
        );
    }
}
