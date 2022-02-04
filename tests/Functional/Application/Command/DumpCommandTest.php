<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Tests\Functional\Application\Command;

use Andriichuk\KeepEnv\Application\Command\DumpCommand;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamFile;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class DumpCommandTest extends TestCase
{
    private vfsStreamDirectory $rootFolder;
    private CommandTester $commandTester;

    protected function setUp(): void
    {
        $this->rootFolder = vfsStream::setup('src');

        $application = new Application();
        $application->add(new DumpCommand());

        $command = $application->find('dump');
        $this->commandTester = new CommandTester($command);
    }

    public function testCommandCanDumpEnvironmentVariablesToFile(): void
    {
        $this->rootFolder->addChild(
            (new vfsStreamFile('keepenv.yaml'))
                ->setContent(
                    file_get_contents(dirname(__DIR__, 3) . '/fixtures/case_8/keepenv.yaml'),
                ),
        );

        $this->commandTester->execute([
            '--env' => 'common',
            '--env-file' => [],
            '--target-env-file' => 'vfs://src/.env.command_test',
            '--env-provider' => 'system',
            '--with-values' => true,
            '--spec' => 'vfs://src/keepenv.yaml'
        ]);

        $this->commandTester->assertCommandIsSuccessful();
        $this->assertFileEquals(
            dirname(__DIR__, 3) . '/fixtures/case_8/.env.command',
            $this->rootFolder->getChild('.env.command_test')->url(),
        );
    }

    public function testCommandCannotWriteEnvironmentVariablesToExistingFile(): void
    {
        $this->rootFolder->addChild((new vfsStreamFile('.env'))->setContent(''));
        $this->rootFolder->addChild(
            (new vfsStreamFile('keepenv.yaml'))
                ->setContent(
                    file_get_contents(dirname(__DIR__, 3) . '/fixtures/case_8/keepenv.yaml'),
                ),
        );

        $this->commandTester->setInputs([
            'no',
        ]);

        $this->commandTester->execute([
            '--env' => 'common',
            '--env-file' => [],
            '--target-env-file' => 'vfs://src/.env',
            '--env-provider' => 'system',
            '--with-values' => true,
            '--spec' => 'vfs://src/keepenv.yaml'
        ]);

        $this->assertEquals(1, $this->commandTester->getStatusCode());
    }
}
