<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Tests\Functional\Manager;

use Andriichuk\KeepEnv\Environment\Writer\EnvFileManager;
use Andriichuk\KeepEnv\Manager\AddNewVariableManager;
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
    private AddNewVariableManager $manager;
    private vfsStreamDirectory $rootFolder;

    protected function setUp(): void
    {
        $this->rootFolder = vfsStream::setup('src');
        $this->rootFolder->addChild(
            (new vfsStreamFile('.env'))
                ->setContent(
                    file_get_contents(dirname(__DIR__, 2) . '/fixtures/common/.env'),
                ),
        );
        $this->rootFolder->addChild(
            (new vfsStreamFile('keepenv.yaml'))
                ->setContent(
                    file_get_contents(dirname(__DIR__, 2) . '/fixtures/common/keepenv.yaml'),
                ),
        );

        $this->manager = new AddNewVariableManager(
            new EnvFileWriter(new EnvFileManager($this->rootFolder->getChild('.env')->url())),
            new SpecYamlReader(new SpecificationArrayBuilder()),
            new SpecYamlWriter(),
        );
    }

    public function testManager(): void
    {
        $this->manager->add(
            new Variable(
                'APP_TEST_KEY',
                'Application test key.',
                false,
                false,
                [
                    'required' => true,
                    'string' => true,
                ]
            ),
            '123qwe',
            'common',
            $this->rootFolder->getChild('keepenv.yaml')->url(),
        );

        $specReader = new SpecYamlReader(new SpecificationArrayBuilder());
        $specification = $specReader->read($this->rootFolder->getChild('keepenv.yaml')->url());

        $this->assertEquals(
            [
                'description' => 'Application test key.',
                'rules' => [
                    'required' => true,
                    'string' => true,
                ],
            ],
            $specification->get('common')->get('APP_TEST_KEY')->toArray(),
        );
    }
}
