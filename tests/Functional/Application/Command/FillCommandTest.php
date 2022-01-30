<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Tests\Functional\Application\Command;

use Andriichuk\KeepEnv\Application\Command\FillCommand;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamFile;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class FillCommandTest extends TestCase
{
    private vfsStreamDirectory $rootFolder;
    private CommandTester $commandTester;

    protected function setUp(): void
    {
        $this->rootFolder = vfsStream::setup('src');
        $this->rootFolder->addChild(
            (new vfsStreamFile('.env'))
                ->setContent(
                    file_get_contents(dirname(__DIR__, 3) . '/fixtures/case_7/.env'),
                ),
        );
        $this->rootFolder->addChild(
            (new vfsStreamFile('keepenv.yaml'))
                ->setContent(
                    file_get_contents(dirname(__DIR__, 3) . '/fixtures/case_7/keepenv.yaml'),
                ),
        );

        $application = new Application();
        $application->add(new FillCommand());

        $command = $application->find('fill');
        $this->commandTester = new CommandTester($command);
    }

    public function testCommandCanFillEmptyEnvVariables(): void
    {
        $this->commandTester->setInputs([
            'KeepEnv Project',
            'local',
            'true',
            '5555',
        ]);

        $this->commandTester->execute([
            '--env' => 'common',
            '--target-env-file' => $this->rootFolder->getChild('.env')->url(),
            '--env-file' => dirname($this->rootFolder->getChild('.env')->url()),
            '--spec' => $this->rootFolder->getChild('keepenv.yaml')->url(),
        ]);

        $this->commandTester->assertCommandIsSuccessful();
        $this->assertFileEquals(
            dirname(__DIR__, 3) . '/fixtures/case_7/.env.result',
            $this->rootFolder->getChild('.env')->url(),
        );
    }
}
