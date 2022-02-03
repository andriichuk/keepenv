<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Tests\Functional\Specification\Reader\Specification;

use Andriichuk\KeepEnv\Specification\Reader\SpecYamlReader;
use Andriichuk\KeepEnv\Specification\SpecificationArrayBuilder;
use InvalidArgumentException;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamFile;
use PHPUnit\Framework\TestCase;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class SpecificationYamlReaderTest extends TestCase
{
    private vfsStreamDirectory $rootFolder;

    protected function setUp(): void
    {
        $this->rootFolder = vfsStream::setup('src');
        $this->rootFolder->addChild(
            (new vfsStreamFile('keepenv.yaml'))
                ->setContent(
                    file_get_contents(
                        dirname(__DIR__, 4) . '/fixtures/case_5/keepenv.yaml',
                    ),
                ),
        );
    }

    public function testReaderCanThrowExceptionOnAttemptToReadMissingFile(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $reader = new SpecYamlReader(new SpecificationArrayBuilder());
        $reader->read('not-exists-keepenv.yaml');
    }

    public function testReaderCanReadFile(): void
    {
        $reader = new SpecYamlReader(new SpecificationArrayBuilder());
        $specification = $reader->read($this->rootFolder->getChild('keepenv.yaml')->url());

        $this->assertEquals(
            [
                'version' => '1.0',
                'environments' => [
                    'common' => [
                        'variables' => [
                            'APP_ENV' => [
                                'description' => 'Application environment.',
                                'rules' => [
                                    'required' => true,
                                    'enum' => ['local', 'production'],
                                ],
                            ],
                            'APP_DEBUG' => [
                                'description' => 'Application debug.',
                                'rules' => [
                                    'required' => true,
                                    'boolean' => true,
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            $specification->toArray()
        );
    }
}
