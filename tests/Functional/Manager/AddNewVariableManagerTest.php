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

    public function testManagerCanAddRegularVariableWithExport(): void
    {
        $this->rootFolder->addChild((new vfsStreamFile('.env'))->setContent('APP_ENV=production'));
        $this->rootFolder->addChild(
            (new vfsStreamFile('keepenv.yaml'))
                ->setContent(
                    file_get_contents(dirname(__DIR__, 2) . '/fixtures/case_9/keepenv_regular_var_init.yaml'),
                ),
        );

        $manager = new AddNewVariableManager(
            new EnvFileWriter(new EnvFileManager($this->rootFolder->getChild('.env')->url())),
            new SpecYamlReader(new SpecificationArrayBuilder()),
            new SpecYamlWriter(),
        );

        $manager->add(
            new Variable(
                'PAYMENT_FEATURE_ENABLED',
                'Payment feature flag',
                true,
                false,
            ),
            'true',
            'common',
            $this->rootFolder->getChild('keepenv.yaml')->url(),
        );

        $this->assertEquals(
            "APP_ENV=production\nexport PAYMENT_FEATURE_ENABLED=true\n",
            file_get_contents($this->rootFolder->getChild('.env')->url()),
        );

        $this->assertFileEquals(
            dirname(__DIR__, 2) . '/fixtures/case_9/keepenv_regular_var_expected.yaml',
            $this->rootFolder->getChild('keepenv.yaml')->url(),
        );
    }

    public function testManagerCanAddSystemVariable(): void
    {
        $envContent = 'APP_ENV=production';

        $this->rootFolder->addChild((new vfsStreamFile('.env'))->setContent($envContent));
        $this->rootFolder->addChild(
            (new vfsStreamFile('keepenv.yaml'))
                ->setContent(
                    file_get_contents(dirname(__DIR__, 2) . '/fixtures/case_9/keepenv_system_var_init.yaml'),
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

        $this->assertFileEquals(
            dirname(__DIR__, 2) . '/fixtures/case_9/keepenv_system_var_expected.yaml',
            $this->rootFolder->getChild('keepenv.yaml')->url(),
        );
    }

    public function testManagerThrowsExceptionWhenVariableAlreadyDefined(): void
    {
        $this->expectException(NewVariablesManagerException::class);

        $this->rootFolder->addChild((new vfsStreamFile('.env'))->setContent(''));
        $this->rootFolder->addChild(
            (new vfsStreamFile('keepenv.yaml'))
                ->setContent(
                    file_get_contents(dirname(__DIR__, 2) . '/fixtures/case_9/keepenv_system_var_init.yaml'),
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

    public function testManagerCanAddNewVariableWithNewEnvironment(): void
    {
        $this->rootFolder->addChild((new vfsStreamFile('.env'))->setContent('APP_ENV=production'));
        $this->rootFolder->addChild(
            (new vfsStreamFile('keepenv.yaml'))
                ->setContent(
                    file_get_contents(dirname(__DIR__, 2) . '/fixtures/case_9/keepenv_new_env_init.yaml'),
                ),
        );

        $manager = new AddNewVariableManager(
            new EnvFileWriter(new EnvFileManager($this->rootFolder->getChild('.env')->url())),
            new SpecYamlReader(new SpecificationArrayBuilder()),
            new SpecYamlWriter(),
        );

        $manager->add(
            new Variable(
                'SALES_FEATURE_ENABLED',
                'Sales feature flag.',
                false,
                false,
                [],
                'OFF',
            ),
            'true',
            'local',
            $this->rootFolder->getChild('keepenv.yaml')->url(),
        );

        $this->assertEquals(
            "APP_ENV=production\nSALES_FEATURE_ENABLED=true\n",
            file_get_contents($this->rootFolder->getChild('.env')->url()),
        );

        $this->assertFileEquals(
            dirname(__DIR__, 2) . '/fixtures/case_9/keepenv_new_env_expected.yaml',
            $this->rootFolder->getChild('keepenv.yaml')->url(),
        );
    }
}
