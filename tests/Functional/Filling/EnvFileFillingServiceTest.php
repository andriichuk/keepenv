<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Tests\Functional\Filling;

use Andriichuk\KeepEnv\Environment\Reader\VlucasPhpDotEnvFileReader;
use Andriichuk\KeepEnv\Environment\Writer\EnvFileManager;
use Andriichuk\KeepEnv\Environment\Writer\EnvFileWriter;
use Andriichuk\KeepEnv\Filling\EnvFileFillingService;
use Andriichuk\KeepEnv\Specification\Reader\SpecYamlReader;
use Andriichuk\KeepEnv\Specification\SpecificationArrayBuilder;
use Andriichuk\KeepEnv\Specification\Variable;
use Andriichuk\KeepEnv\Validation\Exceptions\ValidationReportException;
use Andriichuk\KeepEnv\Validation\Rules\RulesRegistry;
use Andriichuk\KeepEnv\Validation\VariableValidation;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamFile;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class EnvFileFillingServiceTest extends TestCase
{
    private vfsStreamDirectory $rootFolder;
    private EnvFileFillingService $service;

    protected function setUp(): void
    {
        $this->rootFolder = vfsStream::setup('src');
        $this->rootFolder->addChild(
            (new vfsStreamFile('.env'))
                ->setContent(
                    file_get_contents(dirname(__DIR__, 2) . '/fixtures/case_7/.env'),
                ),
        );
        $this->rootFolder->addChild(
            (new vfsStreamFile('keepenv.yaml'))
                ->setContent(
                    file_get_contents(dirname(__DIR__, 2) . '/fixtures/case_7/keepenv.yaml'),
                ),
        );

        $this->service = new EnvFileFillingService(
            new SpecYamlReader(new SpecificationArrayBuilder()),
            new VlucasPhpDotEnvFileReader(),
            new EnvFileWriter(new EnvFileManager($this->rootFolder->getChild('.env')->url())),
            new VariableValidation(RulesRegistry::default()),
        );
    }

    public function testServiceCanFillVariables(): void
    {
        $valueProviders = [
            'APP_NAME' => static fn () => 'KeepEnv Project',
            'APP_ENV' => static fn () => 'local',
            'APP_DEBUG' => static fn () => 'true',
            'REDIS_PORT' => static fn () => '5555',
        ];

        $countOfFilledVariables = $this->service->fill(
            'common',
            dirname($this->rootFolder->getChild('.env')->url()),
            $this->rootFolder->getChild('keepenv.yaml')->url(),
            static function (Variable $variable, callable $validator) use ($valueProviders): string {
                if (!isset($valueProviders[$variable->name])) {
                    throw new RuntimeException("Undefined value provider for key $variable->name.");
                }

                return $validator($valueProviders[$variable->name]());
            },
            static fn (string $message) => '',
        );

        $this->assertEquals(count($valueProviders), $countOfFilledVariables);
        $this->assertFileEquals(
            dirname(__DIR__, 2) . '/fixtures/case_7/.env.result',
            $this->rootFolder->getChild('.env')->url(),
        );
    }

    public function testServiceCanThrowExceptionOnInvalidInput(): void
    {
        $this->expectException(ValidationReportException::class);
        $this->expectExceptionMessage('The value is required.');

        $valueProviders = [
            'APP_NAME' => static fn () => '',
        ];

        $this->service->fill(
            'common',
            dirname($this->rootFolder->getChild('.env')->url()),
            $this->rootFolder->getChild('keepenv.yaml')->url(),
            static function (Variable $variable, callable $validator) use ($valueProviders): string {
                return $validator($valueProviders[$variable->name]());
            },
            static fn (string $message) => '',
        );
    }
}
