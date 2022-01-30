<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Tests\Functional\Application\Command;

use Andriichuk\KeepEnv\Application\Command\ValidateCommand;
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
class ValidateCommandTest extends TestCase
{
    private vfsStreamDirectory $rootFolder;
    private CommandTester $commandTester;

    protected function setUp(): void
    {
        $this->rootFolder = vfsStream::setup('src');

        $application = new Application();
        $application->add(new ValidateCommand());

        $command = $application->find('validate');
        $this->commandTester = new CommandTester($command);
    }

    protected function tearDown(): void
    {
        $this->rootFolder->removeChild('.env');
        $this->rootFolder->removeChild('keepenv_laravel.yaml');

        $_ENV = [];
    }

    public function testCommandCanValidateEnvironment(): void
    {
        $this->rootFolder->addChild(
            (new vfsStreamFile('.env'))
                ->setContent(
                    file_get_contents(dirname(__DIR__, 3) . '/fixtures/case_3/.env'),
                ),
        );

        $this->rootFolder->addChild(
            (new vfsStreamFile('keepenv_laravel.yaml'))
                ->setContent(
                    file_get_contents(dirname(__DIR__, 3) . '/fixtures/case_3/keepenv.yaml'),
                ),
        );

        $this->commandTester->execute([
            'env' => 'local',
            '--env-file' => [dirname($this->rootFolder->getChild('.env')->url())],
            '--spec' => $this->rootFolder->getChild('keepenv_laravel.yaml')->url(),
        ]);

        $this->commandTester->assertCommandIsSuccessful();
    }

    public function testCommandFailEnvironmentValidation(): void
    {
        $this->rootFolder->addChild(
            (new vfsStreamFile('.env'))
                ->setContent(
                    file_get_contents(dirname(__DIR__, 3) . '/fixtures/case_4/.env'),
                ),
        );
        $this->rootFolder->addChild(
            (new vfsStreamFile('keepenv_laravel.yaml'))
                ->setContent(
                    file_get_contents(dirname(__DIR__, 3) . '/fixtures/case_4/keepenv.yaml'),
                ),
        );

        $this->commandTester->execute([
            'env' => 'local',
            '--env-file' => [dirname($this->rootFolder->getChild('.env')->url())],
            '--spec' => $this->rootFolder->getChild('keepenv_laravel.yaml')->url(),
        ]);

        $this->assertEquals(Command::FAILURE, $this->commandTester->getStatusCode());
    }
}
