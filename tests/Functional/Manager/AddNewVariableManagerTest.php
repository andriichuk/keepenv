<?php

declare(strict_types=1);

namespace Andriichuk\Enviro\Functional\Manager;

use Andriichuk\Enviro\Manager\AddNewVariableManager;
use Andriichuk\Enviro\Manager\AddVariableCommand;
use Andriichuk\Enviro\Specification\Reader\SpecificationYamlReader;
use Andriichuk\Enviro\Specification\SpecificationArrayBuilder;
use Andriichuk\Enviro\Specification\Variable;
use Andriichuk\Enviro\Environment\Writer\EnvFileWriter;
use Andriichuk\Enviro\Specification\Writer\SpecificationYamlWriter;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamFile;
use PHPUnit\Framework\TestCase;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class AddNewVariableManagerTest extends TestCase
{
    private AddNewVariableManager $manager;
    private vfsStreamDirectory $rootFolder;

    protected function setUp(): void
    {
        $this->rootFolder = vfsStream::setup('src');
        $this->rootFolder->addChild(
            (new vfsStreamFile('.env'))
                ->setContent(
                    file_get_contents(dirname(__DIR__, 2) . '/stubs/.env'),
                ),
        );
        $this->rootFolder->addChild(
            (new vfsStreamFile('env.spec.yaml'))
                ->setContent(
                    file_get_contents(dirname(__DIR__, 2) . '/stubs/env.spec.yaml'),
                ),
        );

        $this->manager = new AddNewVariableManager(
            new EnvFileWriter($this->rootFolder->getChild('.env')->url()),
            new SpecificationYamlReader(new SpecificationArrayBuilder()),
            new SpecificationYamlWriter(),
        );
    }

    public function testManager(): void
    {
        $this->manager->add(
            new AddVariableCommand(
                new Variable(
                    'APP_TEST_KEY',
                    'Application test key.',
                    false,
                    [
                        'required' => true,
                        'string' => true,
                    ]
                ),
                '123qwe',
                'common',
                $this->rootFolder->getChild('.env')->url(),
                $this->rootFolder->getChild('env.spec.yaml')->url(),
            ),
        );

        $envReader = new EnvFileWriter($this->rootFolder->getChild('.env')->url());
        $specReader = new SpecificationYamlReader(new SpecificationArrayBuilder());
        $specification = $specReader->read($this->rootFolder->getChild('env.spec.yaml')->url());

        $this->assertEquals('123qwe', $envReader->get('APP_TEST_KEY'));
        $this->assertEquals(
            [
                'description' => 'Application test key.',
                'rules' => [
                    'required' => true,
                    'string' => true,
                ],
            ]
            ,
            $specification->get('common')->get('APP_TEST_KEY')->toArray(),
        );
    }
}
