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
        $this->assertFileEquals(
            dirname(__DIR__, 3) . '/fixtures/case_10/keepenv_expected.yaml',
            $this->rootFolder->getChild('keepenv.yaml')->url(),
        );
    }
}
