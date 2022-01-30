<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Tests\Functional\Generator;

use Andriichuk\KeepEnv\Environment\Reader\EnvReaderFactory;
use Andriichuk\KeepEnv\Generator\Presets\PresetFactory;
use Andriichuk\KeepEnv\Generator\SpecGenerator;
use Andriichuk\KeepEnv\Specification\Writer\SpecWriterFactory;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamFile;
use PHPUnit\Framework\TestCase;
use RuntimeException;

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

    public function testEnvironmentSpecificationGeneration(): void
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
            (new EnvReaderFactory())->make('auto'),
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
            dirname(__DIR__, 2) . '/fixtures/case_1/keepenv.yaml',
            'vfs://src/keepenv.yaml',
        );
    }

    public function testEnvironmentSpecificationWithLaravelPresetGeneration(): void
    {
        $this->rootFolder->addChild(
            (new vfsStreamFile('.env'))
                ->setContent(
                    file_get_contents(
                        dirname(__DIR__, 2) . '/fixtures/case_2/.env',
                    ),
                ),
        );

        $specGenerator = new SpecGenerator(
            (new EnvReaderFactory())->make('auto'),
            (new SpecWriterFactory())->basedOnResource('vfs://src/keepenv_laravel.yaml'),
            new PresetFactory(),
        );

        $specGenerator->generate(
            'common',
            [dirname($this->rootFolder->getChild('.env')->url())],
            'vfs://src/keepenv_laravel.yaml',
            static fn () => true,
            'laravel'
        );

        $this->assertFileEquals(
            dirname(__DIR__, 2) . '/fixtures/case_2/keepenv_laravel.yaml',
            'vfs://src/keepenv_laravel.yaml',
        );
    }

    public function testEnvironmentSpecificationWithSymfonyPresetGeneration(): void
    {
        $this->rootFolder->addChild(
            (new vfsStreamFile('.env.symfony'))
                ->setContent(
                    file_get_contents(
                        dirname(__DIR__, 2) . '/fixtures/case_2/.env.symfony',
                    ),
                ),
        );

        $specGenerator = new SpecGenerator(
            (new EnvReaderFactory())->make('symfony/dotenv'),
            (new SpecWriterFactory())->basedOnResource('vfs://src/keepenv_symfony.yaml'),
            new PresetFactory(),
        );

        $specGenerator->generate(
            'common',
            [$this->rootFolder->getChild('.env.symfony')->url()],
            'vfs://src/keepenv_symfony.yaml',
            static fn () => true,
            'symfony'
        );

        $this->assertFileEquals(
            dirname(__DIR__, 2) . '/fixtures/case_2/keepenv_symfony.yaml',
            'vfs://src/keepenv_symfony.yaml',
        );
    }

    public function testGeneratorThrowsExceptionOnAlreadyExistingSpecFile(): void
    {
        $this->expectException(RuntimeException::class);

        $this->rootFolder->addChild((new vfsStreamFile('.env'))->setContent(''));
        $this->rootFolder->addChild((new vfsStreamFile('keepenv.yaml'))->setContent(''));

        $specGenerator = new SpecGenerator(
            (new EnvReaderFactory())->make('auto'),
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
