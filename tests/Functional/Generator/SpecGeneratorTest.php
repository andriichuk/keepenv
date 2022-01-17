<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Functional\Generator;

use Andriichuk\KeepEnv\Environment\Reader\EnvReaderFactory;
use Andriichuk\KeepEnv\Generator\Presets\PresetFactory;
use Andriichuk\KeepEnv\Generator\SpecGenerator;
use Andriichuk\KeepEnv\Specification\Writer\SpecWriterFactory;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamFile;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class SpecGeneratorTest extends TestCase
{
    private vfsStreamDirectory $rootFolder;
    private SpecGenerator $specGenerator;

    protected function setUp(): void
    {
        $this->rootFolder = vfsStream::setup('src');
        $this->rootFolder->addChild(
            (new vfsStreamFile('.env'))
                ->setContent(
                    file_get_contents(
                        dirname(__DIR__, 2) . '/fixtures/common/.env',
                    ),
                ),
        );
        $this->rootFolder->addChild(
            (new vfsStreamFile('env.spec.yaml'))
                ->setContent(''),
        );

        $envReaderFactory = new EnvReaderFactory();
        $writerFactory = new SpecWriterFactory();

        $this->specGenerator = new SpecGenerator(
            $envReaderFactory->baseOnAvailability(),
            $writerFactory->basedOnResource($this->rootFolder->getChild('env.spec.yaml')->url()),
            new PresetFactory(),
        );
    }

    public function testEnvironmentSpecificationGeneration(): void
    {
        $this->specGenerator->generate(
            'common',
            [dirname($this->rootFolder->getChild('.env')->url())],
            $this->rootFolder->getChild('env.spec.yaml')->url(),
            static fn (): bool => true,
        );

        $this->assertEquals(
            file_get_contents(dirname(__DIR__, 2) . '/fixtures/case_1/env.spec.yaml'),
            file_get_contents($this->rootFolder->getChild('env.spec.yaml')->url())
        );
    }

    public function testEnvironmentSpecificationWithLaravelPresetGeneration(): void
    {
        $this->specGenerator->generate(
            'common',
            [dirname($this->rootFolder->getChild('.env')->url())],
            $this->rootFolder->getChild('env.spec.yaml')->url(),
            static fn () => true,
            'laravel'
        );

        $this->assertEquals(
            file_get_contents(dirname(__DIR__, 2) . '/fixtures/case_2/env.spec.yaml'),
            file_get_contents($this->rootFolder->getChild('env.spec.yaml')->url())
        );
    }

    public function testGeneratorThrowsExceptionOnAlreadyExistingSpecFile(): void
    {
        $this->expectException(RuntimeException::class);

        $this->specGenerator->generate(
            'common',
            [dirname($this->rootFolder->getChild('.env')->url())],
            $this->rootFolder->getChild('env.spec.yaml')->url(),
            static fn () => false,
        );
    }
}