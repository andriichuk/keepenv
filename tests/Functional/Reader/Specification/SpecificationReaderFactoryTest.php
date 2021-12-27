<?php

declare(strict_types=1);

namespace Andriichuk\Enviro\Functional\Reader\Specification;

use Andriichuk\Enviro\Reader\Specification\SpecificationReaderFactory;
use Andriichuk\Enviro\Reader\Specification\SpecificationYamlReader;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamFile;
use OutOfBoundsException;
use PHPUnit\Framework\TestCase;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class SpecificationReaderFactoryTest extends TestCase
{
    private vfsStreamDirectory $rootFolder;

    protected function setUp(): void
    {
        $this->rootFolder = vfsStream::setup('src');
    }

    public function testMakeReaderFromYamlFile(): void
    {
        $this->rootFolder->addChild(new vfsStreamFile('env.spec.yaml'));

        $factory = new SpecificationReaderFactory();
        $reader = $factory->basedOnResource($this->rootFolder->getChild('env.spec.yaml')->url());

        $this->assertInstanceOf(SpecificationYamlReader::class, $reader);
    }

    public function testExceptionThrownForUnsupportedFileType(): void
    {
        $this->expectException(OutOfBoundsException::class);

        $this->rootFolder->addChild(new vfsStreamFile('env.spec.xml'));

        $factory = new SpecificationReaderFactory();
        $factory->basedOnResource($this->rootFolder->getChild('env.spec.xml')->url());
    }
}
