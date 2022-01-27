<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Functional\Environment\Writer;

use Andriichuk\KeepEnv\Environment\Writer\EnvFileWriter;
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
                    file_get_contents(dirname(__DIR__, 3) . '/fixtures/common/.env'),
                ),
        );

        $this->writer = new EnvFileWriter($this->rootFolder->getChild('.env')->url());
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
