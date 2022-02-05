<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Tests\Functional\Environment\Writer;

use Andriichuk\KeepEnv\Environment\Writer\EnvFileManager;
use Andriichuk\KeepEnv\Environment\Writer\EnvFileWriter;
use Andriichuk\KeepEnv\Environment\Writer\Exceptions\EnvFileWriterException;
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

        $this->writer = new EnvFileWriter(new EnvFileManager($this->rootFolder->getChild('.env')->url()));
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

    public function testWriterCanSaveNewVariable(): void
    {
        $this->writer->save('API_TEST_KEY', '12345');

        $this->assertTrue($this->writer->has('API_TEST_KEY'));
        $this->assertIsInt(
            mb_strpos(
                file_get_contents($this->rootFolder->getChild('.env')->url()),
                'API_TEST_KEY=12345',
            )
        );
    }

    public function testWriterCanSaveExistingVariable(): void
    {
        $this->writer->save('APP_ENV', 'staging');

        $this->assertTrue($this->writer->has('APP_ENV'));
        $this->assertIsInt(
            mb_strpos(
                file_get_contents($this->rootFolder->getChild('.env')->url()),
                'APP_ENV=staging',
            )
        );
    }

    public function testWriterCanAddNewVariable(): void
    {
        $this->writer->add('API_TEST_NEW_KEY', '1234567');

        $this->assertTrue($this->writer->has('API_TEST_NEW_KEY'));
        $this->assertIsInt(
            mb_strpos(
                file_get_contents($this->rootFolder->getChild('.env')->url()),
                'API_TEST_NEW_KEY=1234567',
            )
        );
    }

    public function testWriterCanUpdateExistingVariable(): void
    {
        $this->writer->update('APP_ENV', 'production_new');

        $this->assertTrue($this->writer->has('APP_ENV'));
        $this->assertIsInt(
            mb_strpos(
                file_get_contents($this->rootFolder->getChild('.env')->url()),
                'APP_ENV=production_new',
            )
        );
    }

    public function testWriterCanQuoteValuesWithSpace(): void
    {
        $this->writer->add('TEST_KEY_WITH_SPACE', 'demo value');

        $this->assertTrue($this->writer->has('TEST_KEY_WITH_SPACE'));
        $this->assertIsInt(
            mb_strpos(
                file_get_contents($this->rootFolder->getChild('.env')->url()),
                'TEST_KEY_WITH_SPACE="demo value"',
            )
        );
    }

    public function testWriterCanAddBatchOfVariables(): void
    {
        $this->writer->addBatch([
            'TEST_NUMERIC_KEY' => ['value' => '12345', 'export' => false],
            'TEST_EMPTY_STRING_KEY' => ['value' => '', 'export' => false],
            'TEST_STRING_WITH_SPACE_KEY' => ['value' => 'string with space', 'export' => true],
        ], true);

        $this->assertTrue($this->writer->has('TEST_NUMERIC_KEY'));
        $this->assertTrue($this->writer->has('TEST_EMPTY_STRING_KEY'));
        $this->assertTrue($this->writer->has('TEST_STRING_WITH_SPACE_KEY'));

        $content = file_get_contents($this->rootFolder->getChild('.env')->url());

        $this->assertIsInt(mb_strpos($content, 'TEST_NUMERIC_KEY=12345'));
        $this->assertIsInt(mb_strpos($content, 'TEST_EMPTY_STRING_KEY='));
        $this->assertIsInt(mb_strpos($content, 'export TEST_STRING_WITH_SPACE_KEY="string with space"'));
    }

    public function testWriterThrowExceptionOnExistingKeyInBatch(): void
    {
        $this->expectException(EnvFileWriterException::class);

        $this->writer->addBatch([
            'TEST_NUMERIC_KEY' => ['value' => '12345', 'export' => false],
            'APP_NAME' => ['value' => 'dev', 'export' => false],
        ], false);
    }

    public function testWriterThrowsExceptionWhenKeyAlreadyExists(): void
    {
        $this->expectException(EnvFileWriterException::class);
        $this->writer->add('APP_ENV', 'new value');
    }
}
