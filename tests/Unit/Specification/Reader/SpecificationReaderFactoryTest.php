<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Unit\Specification\Reader;

use Andriichuk\KeepEnv\Specification\Reader\Exceptions\SpecificationReaderException;
use Andriichuk\KeepEnv\Specification\Reader\SpecificationPhpArrayReader;
use Andriichuk\KeepEnv\Specification\Reader\SpecificationReaderFactory;
use Andriichuk\KeepEnv\Specification\Reader\SpecificationYamlReader;
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
        $factory = new SpecificationReaderFactory();
        $reader = $factory->basedOnSource($sourcePath);

        $this->assertInstanceOf($expectedType, $reader, $message);
    }

    public function readerSourcesProvider(): Generator
    {
        yield [
            'source_path' => '/home/user/env.spec.php',
            'expected_type' => SpecificationPhpArrayReader::class,
            'message' => 'PHP source file.',
        ];

        yield [
            'source_path' => '/home/user/env.spec.yaml',
            'expected_type' => SpecificationYamlReader::class,
            'message' => 'Yaml source file.',
        ];

        yield [
            'source_path' => '/home/user/env.spec.yml',
            'expected_type' => SpecificationYamlReader::class,
            'message' => 'Yml source file.',
        ];
    }

    public function testFactoryThrowExceptionOnWrongSourceType(): void
    {
        $this->expectException(SpecificationReaderException::class);

        $factory = new SpecificationReaderFactory();
        $factory->basedOnSource('/home/user/env.spec.cvs');
    }
}
