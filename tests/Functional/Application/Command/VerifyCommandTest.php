<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Functional\Application\Command;

use Andriichuk\KeepEnv\Application\Command\VerifyCommand;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamFile;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class VerifyCommandTest extends TestCase
{
    private vfsStreamDirectory $rootFolder;
    private CommandTester $commandTester;

    protected function setUp(): void
    {
        $this->rootFolder = vfsStream::setup('src');

        $application = new Application();
        $application->add(new VerifyCommand());

        $command = $application->find('verify');
        $this->commandTester = new CommandTester($command);
    }

    protected function tearDown(): void
    {
        $this->rootFolder->removeChild('.env');
        $this->rootFolder->removeChild('env.spec.yaml');

        $_ENV = [];
    }

    public function testCommandSuccessfullyVerifyEnvironment(): void
    {
        $this->rootFolder->addChild(
            (new vfsStreamFile('.env'))
                ->setContent(
                    file_get_contents(dirname(__DIR__, 3) . '/fixtures/case_3/.env'),
                ),
        );

        $this->rootFolder->addChild(
            (new vfsStreamFile('env.spec.yaml'))
                ->setContent(
                    file_get_contents(dirname(__DIR__, 3) . '/fixtures/case_3/env.spec.yaml'),
                ),
        );

        $this->commandTester->execute([
            'env' => 'local',
            '--env-file' => [dirname($this->rootFolder->getChild('.env')->url())],
            '--spec' => $this->rootFolder->getChild('env.spec.yaml')->url(),
        ]);

        $this->commandTester->assertCommandIsSuccessful();
    }

    public function testCommandFailedVerifyEnvironment(): void
    {
        $this->rootFolder->addChild(
            (new vfsStreamFile('.env'))
                ->setContent(
                    file_get_contents(dirname(__DIR__, 3) . '/fixtures/case_4/.env'),
                ),
        );
        $this->rootFolder->addChild(
            (new vfsStreamFile('env.spec.yaml'))
                ->setContent(
                    file_get_contents(dirname(__DIR__, 3) . '/fixtures/case_4/env.spec.yaml'),
                ),
        );

        $this->commandTester->execute([
            'env' => 'local',
            '--env-file' => [dirname($this->rootFolder->getChild('.env')->url())],
            '--spec' => $this->rootFolder->getChild('env.spec.yaml')->url(),
        ]);

        $this->assertEquals(Command::FAILURE, $this->commandTester->getStatusCode());
    }
}
