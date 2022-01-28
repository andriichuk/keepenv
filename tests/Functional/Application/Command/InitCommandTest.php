<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Functional\Application\Command;

use Andriichuk\KeepEnv\Application\Command\InitCommand;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamFile;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class InitCommandTest extends TestCase
{
    private vfsStreamDirectory $rootFolder;
    private CommandTester $commandTester;

    protected function setUp(): void
    {
        $this->rootFolder = vfsStream::setup('src');

        $application = new Application();
        $application->add(new InitCommand());

        $command = $application->find('init');
        $this->commandTester = new CommandTester($command);
    }

    public function testCommandCanVerifyEnvironment(): void
    {
        $this->rootFolder->addChild(
            (new vfsStreamFile('.env'))
                ->setContent(
                    file_get_contents(dirname(__DIR__, 3) . '/fixtures/case_6/.env'),
                ),
        );

        $this->commandTester->execute([
            '--env' => 'common',
            '--env-file' => [dirname($this->rootFolder->getChild('.env')->url())],
            '--spec' => 'vfs://src/keepenv.yaml',
        ]);

        $this->commandTester->assertCommandIsSuccessful();
        $this->assertEquals(
            file_get_contents(dirname(__DIR__, 3) . '/fixtures/case_6/keepenv.yaml'),
            file_get_contents($this->rootFolder->getChild('keepenv.yaml')->url()),
        );
    }
}
