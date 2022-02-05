<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Tests\Functional\Manager;

use Andriichuk\KeepEnv\Environment\Writer\EnvFileManager;
use Andriichuk\KeepEnv\Manager\AddNewVariableManager;
use Andriichuk\KeepEnv\Manager\Exceptions\NewVariablesManagerException;
use Andriichuk\KeepEnv\Specification\Reader\SpecYamlReader;
use Andriichuk\KeepEnv\Specification\SpecificationArrayBuilder;
use Andriichuk\KeepEnv\Specification\Variable;
use Andriichuk\KeepEnv\Environment\Writer\EnvFileWriter;
use Andriichuk\KeepEnv\Specification\Writer\SpecYamlWriter;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamFile;
use PHPUnit\Framework\TestCase;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class AddNewVariableManagerTest extends TestCase
{
    private vfsStreamDirectory $rootFolder;

    protected function setUp(): void
    {
        $this->rootFolder = vfsStream::setup('src');
    }

    public function testManagerCanAddSystemVariable(): void
    {
        $envContent = 'APP_ENV=production';

        $this->rootFolder->addChild((new vfsStreamFile('.env'))->setContent($envContent));
        $this->rootFolder->addChild(
            (new vfsStreamFile('keepenv.yaml'))
                ->setContent(
                    <<<SPEC
version: '1.0'
environments:
    common:
        variables:
            APP_NAME:
                description: 'Application name.'

SPEC
                ),
        );

        $manager = new AddNewVariableManager(
            new EnvFileWriter(new EnvFileManager($this->rootFolder->getChild('.env')->url())),
            new SpecYamlReader(new SpecificationArrayBuilder()),
            new SpecYamlWriter(),
        );

        $manager->add(
            new Variable(
                'APP_SYSTEM_KEY',
                'Application system key.',
                false,
                true,
            ),
            'qwe123',
            'common',
            $this->rootFolder->getChild('keepenv.yaml')->url(),
        );

        $this->assertEquals(
            $envContent,
            file_get_contents($this->rootFolder->getChild('.env')->url()),
        );

        $this->assertEquals(
            <<<SPEC
version: '1.0'
environments:
    common:
        variables:
            APP_NAME:
                description: 'Application name.'
            APP_SYSTEM_KEY:
                description: 'Application system key.'
                system: true

SPEC,
            file_get_contents($this->rootFolder->getChild('keepenv.yaml')->url()),
        );
    }

    public function testManagerThrowsExceptionWhenVariableAlreadyDefined(): void
    {
        $this->expectException(NewVariablesManagerException::class);

        $this->rootFolder->addChild((new vfsStreamFile('.env'))->setContent(''));
        $this->rootFolder->addChild(
            (new vfsStreamFile('keepenv.yaml'))
                ->setContent(
                    <<<SPEC
version: '1.0'
environments:
    common:
        variables:
            APP_NAME:
                description: 'Application name.'
SPEC
                ),
        );

        $manager = new AddNewVariableManager(
            new EnvFileWriter(new EnvFileManager($this->rootFolder->getChild('.env')->url())),
            new SpecYamlReader(new SpecificationArrayBuilder()),
            new SpecYamlWriter(),
        );

        $manager->add(
            new Variable('APP_NAME', 'Application name.'),
            '123qwe',
            'common',
            $this->rootFolder->getChild('keepenv.yaml')->url(),
        );
    }
}
