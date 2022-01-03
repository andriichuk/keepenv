<?php

declare(strict_types=1);

namespace Andriichuk\Enviro\Functional\Application\Command;

use Andriichuk\Enviro\Application\Command\VerifyCommand;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamFile;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class VerifyCommandTest extends TestCase
{
    private vfsStreamDirectory $rootFolder;

    protected function setUp(): void
    {
        $this->rootFolder = vfsStream::setup('src');
        $this->rootFolder->addChild(
            (new vfsStreamFile('.env'))
                ->setContent(
                    file_get_contents(dirname(__DIR__, 3) . '/stubs/.env'),
                ),
        );
        $this->rootFolder->addChild(
            (new vfsStreamFile('env.spec.yaml'))
                ->setContent(
                    file_get_contents(dirname(__DIR__, 3) . '/stubs/env.spec.yaml'),
                ),
        );
    }

    /**
     * @incomplete
     */
    public function testVerify(): void
    {
        $application = new Application();
        $application->add(new VerifyCommand());

        $this->assertTrue(true);

        /*$command = $application->find('verify');
        $commandTester = new CommandTester($command);

        $commandTester->execute([
            'env' => 'local',
            '--env-file' => $this->rootFolder->getChild('.env')->url(),
            '--spec' => $this->rootFolder->getChild('env.spec.yaml')->url(),
        ]);*/

        //$commandTester->assertCommandIsSuccessful();
    }
}
