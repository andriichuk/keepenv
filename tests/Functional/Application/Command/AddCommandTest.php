<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Tests\Functional\Application\Command;

use Andriichuk\KeepEnv\Application\Command\AddCommand;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamFile;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class AddCommandTest extends TestCase
{
    private vfsStreamDirectory $rootFolder;
    private CommandTester $commandTester;

    protected function setUp(): void
    {
        $this->rootFolder = vfsStream::setup('src');

        $application = new Application();
        $application->add(new AddCommand());

        $command = $application->find('add');
        $this->commandTester = new CommandTester($command);
    }

    public function testCommandCanAddRegularVariable(): void
    {
        $this->rootFolder->addChild((new vfsStreamFile('.env'))->setContent('APP_NAME=KeepEnv'));
        $this->rootFolder->addChild(
            (new vfsStreamFile('keepenv.yaml'))
                ->setContent(
                    file_get_contents(dirname(__DIR__, 3) . '/fixtures/case_10/keepenv_init.yaml')
                )
        );

        $this->commandTester->setInputs([
            'DEMO_MODE', // name
            'Demo mode.', // description
            'yes', // required
            'no', // export
            'no', // system
            '2', // boolean type
            'ON', // value
            'no', // stop adding
        ]);

        $this->commandTester->execute([
            '--env' => 'common',
            '--target-env-file' => $this->rootFolder->getChild('.env')->url(),
            '--spec' => $this->rootFolder->getChild('keepenv.yaml')->url(),
        ]);

        $this->commandTester->assertCommandIsSuccessful();
        $this->assertEquals(
            "APP_NAME=KeepEnv\nDEMO_MODE=ON\n",
            file_get_contents($this->rootFolder->getChild('.env')->url()),
        );
        $this->assertFileEquals(
            dirname(__DIR__, 3) . '/fixtures/case_10/keepenv_expected.yaml',
            $this->rootFolder->getChild('keepenv.yaml')->url(),
        );
    }

    public function testCommandCanValidateAndAskNewValueOnFail(): void
    {
        $this->rootFolder->addChild((new vfsStreamFile('.env'))->setContent('APP_NAME=KeepEnv'));
        $this->rootFolder->addChild(
            (new vfsStreamFile('keepenv.yaml'))
                ->setContent(
                    file_get_contents(dirname(__DIR__, 3) . '/fixtures/case_10/keepenv_init.yaml')
                )
        );

        $this->commandTester->setInputs([
            'DEMO_MODE', // name
            'Demo mode.', // description
            'yes', // required
            'no', // export
            'no', // system
            '2', // boolean type
            'Y', // wrong value
            'ON', // valid value
            'no', // stop adding
        ]);

        $this->commandTester->execute([
            '--env' => 'common',
            '--target-env-file' => $this->rootFolder->getChild('.env')->url(),
            '--spec' => $this->rootFolder->getChild('keepenv.yaml')->url(),
        ]);

        $this->commandTester->assertCommandIsSuccessful();
        $this->assertEquals(
            "APP_NAME=KeepEnv\nDEMO_MODE=ON\n",
            file_get_contents($this->rootFolder->getChild('.env')->url()),
        );
        $this->assertFileEquals(
            dirname(__DIR__, 3) . '/fixtures/case_10/keepenv_expected.yaml',
            $this->rootFolder->getChild('keepenv.yaml')->url(),
        );
    }

    public function testCommandCanPreventOfAddingSameVariable(): void
    {
        $this->rootFolder->addChild((new vfsStreamFile('.env'))->setContent('APP_NAME=KeepEnv'));
        $this->rootFolder->addChild(
            (new vfsStreamFile('keepenv.yaml'))
                ->setContent(
                    file_get_contents(dirname(__DIR__, 3) . '/fixtures/case_10/keepenv_init.yaml')
                )
        );

        $this->commandTester->setInputs([
            'APP_NAME', // name
            'Application name.', // description
            'yes', // required
            'no', // export
            'no', // system
            '0', // string type
            'KeepEnv', // value
            'no', // stop adding
        ]);

        $this->commandTester->execute([
            '--env' => 'common',
            '--target-env-file' => $this->rootFolder->getChild('.env')->url(),
            '--spec' => $this->rootFolder->getChild('keepenv.yaml')->url(),
        ]);

        $this->commandTester->assertCommandIsSuccessful();
        $this->assertEquals(
            "APP_NAME=KeepEnv",
            file_get_contents($this->rootFolder->getChild('.env')->url()),
        );
        $this->assertFileEquals(
            dirname(__DIR__, 3) . '/fixtures/case_10/keepenv_init.yaml',
            $this->rootFolder->getChild('keepenv.yaml')->url(),
        );
    }

    public function testCommandCanAddSeveralVariables(): void
    {
        $this->rootFolder->addChild((new vfsStreamFile('.env'))->setContent('APP_NAME=KeepEnv'));
        $this->rootFolder->addChild(
            (new vfsStreamFile('keepenv.yaml'))
                ->setContent(
                    file_get_contents(dirname(__DIR__, 3) . '/fixtures/case_10/keepenv_init.yaml')
                )
        );

        $this->commandTester->setInputs([
            'APP_LOCALE', // name
            'Application default locale.', // description
            'yes', // required
            'no', // export
            'no', // system
            '0', // string type
            'en', // value

            'yes', // continue adding

            'APP_TIMEZONE', // name
            'Application default timezone.', // description
            'yes', // required
            'no', // export
            'no', // system
            '0', // string type
            'UTC', // value
            'no', // stop adding
        ]);

        $this->commandTester->execute([
            '--env' => 'common',
            '--target-env-file' => $this->rootFolder->getChild('.env')->url(),
            '--spec' => $this->rootFolder->getChild('keepenv.yaml')->url(),
        ]);

        $this->commandTester->assertCommandIsSuccessful();
        $this->assertEquals(
            "APP_NAME=KeepEnv\nAPP_LOCALE=en\nAPP_TIMEZONE=UTC\n",
            file_get_contents($this->rootFolder->getChild('.env')->url()),
        );
        $this->assertFileEquals(
            dirname(__DIR__, 3) . '/fixtures/case_10/keepenv_multiple_expected.yaml',
            $this->rootFolder->getChild('keepenv.yaml')->url(),
        );
    }

    public function testCommandCanAskForEnumOptions(): void
    {
        $this->rootFolder->addChild((new vfsStreamFile('.env'))->setContent('APP_NAME=KeepEnv'));
        $this->rootFolder->addChild(
            (new vfsStreamFile('keepenv.yaml'))
                ->setContent(
                    file_get_contents(dirname(__DIR__, 3) . '/fixtures/case_10/keepenv_init.yaml')
                )
        );

        $this->commandTester->setInputs([
            'APP_ENV', // name
            'Application environment name.', // description
            'yes', // required
            'no', // export
            'no', // system
            '3', // enum type
            'dev', // first enum option
            'yes', // continue adding enum options
            'prod', // second enum option
            'no', // stop adding enum options
            'dev', // value
            'no', // stop adding
        ]);

        $this->commandTester->execute([
            '--env' => 'common',
            '--target-env-file' => $this->rootFolder->getChild('.env')->url(),
            '--spec' => $this->rootFolder->getChild('keepenv.yaml')->url(),
        ]);

        $this->commandTester->assertCommandIsSuccessful();
        $this->assertEquals(
            "APP_NAME=KeepEnv\nAPP_ENV=dev\n",
            file_get_contents($this->rootFolder->getChild('.env')->url()),
        );
        $this->assertFileEquals(
            dirname(__DIR__, 3) . '/fixtures/case_10/keepenv_enum_expected.yaml',
            $this->rootFolder->getChild('keepenv.yaml')->url(),
        );
    }
}
