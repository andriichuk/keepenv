<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Functional\Environment\Writer;

use Andriichuk\KeepEnv\Environment\Writer\EnvFileManager;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamFile;
use PHPUnit\Framework\TestCase;

class EnvFileManagerTest extends TestCase
{
    private vfsStreamDirectory $rootFolder;

    protected function setUp(): void
    {
        $this->rootFolder = vfsStream::setup('src');
    }

    public function testManagerCanCheckFileExistence(): void
    {
        $this->rootFolder->addChild((new vfsStreamFile('.env'))->setContent(''));

        $managerWithExistingFile = new EnvFileManager($this->rootFolder->getChild('.env')->url());
        $managerWithMissingFile = new EnvFileManager('vfs://src/.env.test');

        $this->assertTrue($managerWithExistingFile->exists());
        $this->assertFalse($managerWithMissingFile->exists());
    }

    public function testManagerCanReadContentFromFile(): void
    {
        $line = 'APP_READ_CONTENT="test value"';
        $this->rootFolder->addChild(
            (new vfsStreamFile('.env'))
                ->setContent($line)
        );
        $manager = new EnvFileManager($this->rootFolder->getChild('.env')->url());

        $this->assertEquals($line, $manager->content());
    }

    public function testManagerCanWriteContentToFile(): void
    {
        $lines = 'APP_READ_CONTENT="test value"' . PHP_EOL . 'APP_WRITE_CONTENT=test';

        $this->rootFolder->addChild((new vfsStreamFile('.env'))->setContent(''));
        $manager = new EnvFileManager($this->rootFolder->getChild('.env')->url());
        $manager->write($lines);

        $this->assertEquals($lines, file_get_contents($this->rootFolder->getChild('.env')->url()));
    }

    public function testManagerCanCreateFileIfItDoesNotExists(): void
    {
        $this->rootFolder->addChild((new vfsStreamFile('.env'))->setContent(''));
        $manager = new EnvFileManager('vfs://src/.env.new_test');
        $manager->createIfNotExists();

        $this->assertFileExists('vfs://src/.env.new_test');
    }
}
