<?php

declare(strict_types=1);

namespace Andriichuk\Enviro\Functional\Reader\Specification;

use Andriichuk\Enviro\Reader\Specification\SpecificationYamlReader;
use Andriichuk\Enviro\Specification\SpecificationArrayBuilder;
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
            (new vfsStreamFile('env.spec.yaml'))
                ->setContent(
                    file_get_contents(
                        dirname(__DIR__, 3) . '/stubs/env.spec.yaml',
                    ),
                ),
        );
    }

    public function testExceptionThrownOnAttemptToReadMissingFile(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $reader = new SpecificationYamlReader(new SpecificationArrayBuilder());
        $reader->read('not-exists-env.spec.yaml');
    }

    public function testReadingFlow(): void
    {
        $reader = new SpecificationYamlReader(new SpecificationArrayBuilder());
        $specification = $reader->read($this->rootFolder->getChild('env.spec.yaml')->url());

        $this->assertEquals(
            [
                'version' => '1.0',
                'environments' => [
                    'common' => [
                        'APP_ENV' => [
                            'description' => 'Application environment',
                            'default' => 'production',
                            'rules' => [
                                'required' => true,
                                'enum' => ['local', 'production'],
                            ],
                        ],
                        'APP_DEBUG' => [
                            'description' => 'Application debug mode.',
                            'default' => 'true',
                            'rules' => [
                                'required' => true,
                                'enum' => ['true', 'false'],
                            ],
                        ],
                    ],
                    'local' => [
                        'MAIL_HOST' => [
                            'description' => 'Main host.',
                            'rules' => [
                                'equals' => 'mailhog',
                            ],
                        ],
                    ],
                    'production' => [
                        'APP_DEBUG' => [
                            'rules' => [
                                'equals' => 'false',
                            ],
                        ],
                    ],
                ],
            ],
            $specification->toArray()
        );
    }
}
