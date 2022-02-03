<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Tests\Unit\Specification\Reader;

use Andriichuk\KeepEnv\Specification\Reader\Exceptions\SpecReaderException;
use Andriichuk\KeepEnv\Specification\Reader\SpecReaderFactory;
use Andriichuk\KeepEnv\Specification\Reader\SpecYamlReader;
use Generator;
use PHPUnit\Framework\TestCase;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class SpecificationReaderFactoryTest extends TestCase
{
    /**
     * @dataProvider readerSourcesProvider
     */
    public function testFactoryCanCreateReaderBasedOnSourceType(string $sourcePath, string $expectedType, string $message): void
    {
        $factory = new SpecReaderFactory();
        $reader = $factory->basedOnSource($sourcePath);

        $this->assertInstanceOf($expectedType, $reader, $message);
    }

    public function readerSourcesProvider(): Generator
    {
        yield [
            'source_path' => '/home/user/keepenv_laravel.yaml',
            'expected_type' => SpecYamlReader::class,
            'message' => 'Yaml source file.',
        ];

        yield [
            'source_path' => '/home/user/env.spec.yml',
            'expected_type' => SpecYamlReader::class,
            'message' => 'Yml source file.',
        ];
    }

    public function testFactoryThrowExceptionOnWrongSourceType(): void
    {
        $this->expectException(SpecReaderException::class);

        $factory = new SpecReaderFactory();
        $factory->basedOnSource('/home/user/env.spec.cvs');
    }
}
