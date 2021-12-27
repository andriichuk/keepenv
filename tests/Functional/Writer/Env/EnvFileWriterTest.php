<?php

declare(strict_types=1);

namespace Andriichuk\Enviro\Functional\Writer\Env;

use Andriichuk\Enviro\Writer\Env\EnvFileWriter;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamFile;
use PHPUnit\Framework\TestCase;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class EnvFileWriterTest extends TestCase
{
    private vfsStreamDirectory $rootFolder;
    private EnvFileWriter $writer;

    protected function setUp(): void
    {
        $this->rootFolder = vfsStream::setup('src');
        $this->rootFolder->addChild(
            (new vfsStreamFile('.env'))
                ->setContent(
                    file_get_contents(dirname(__DIR__, 3) . '/stubs/.env'),
                ),
        );

        $this->writer = new EnvFileWriter($this->rootFolder->getChild('.env')->url());
    }

    public function testVariableReading(): void
    {
        $this->assertEquals('production', $this->writer->get('APP_ENV'));
        $this->assertIsInt(
            mb_strpos(
                file_get_contents($this->rootFolder->getChild('.env')->url()),
                'APP_ENV=production',
            )
        );
    }

    public function testNewVariableAdding(): void
    {
        $this->writer->add('WRITE_TEST_KEY', '321ewq');

        $this->assertEquals('321ewq', $this->writer->get('WRITE_TEST_KEY'));
        $this->assertIsInt(
            mb_strpos(
                file_get_contents($this->rootFolder->getChild('.env')->url()),
                'WRITE_TEST_KEY=321ewq',
            )
        );
    }

    public function testVariableRemoval(): void
    {
        $this->writer->remove('APP_ENV');

        $this->assertNull($this->writer->get('APP_ENV'));
        $this->assertFalse(
            mb_strpos(
                file_get_contents($this->rootFolder->getChild('.env')->url()),
                'APP_ENV=',
            )
        );
    }

    public function testVariableExistenceCheck(): void
    {
        $this->assertTrue($this->writer->has('APP_ENV'));
        $this->assertIsInt(
            mb_strpos(
                file_get_contents($this->rootFolder->getChild('.env')->url()),
                'APP_ENV=',
            )
        );
    }
}
