<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Tests\Unit\Specification\Writer;

use Andriichuk\KeepEnv\Specification\Writer\Exceptions\SpecFactoryException;
use Andriichuk\KeepEnv\Specification\Writer\SpecWriterFactory;
use Andriichuk\KeepEnv\Specification\Writer\SpecYamlWriter;
use PHPUnit\Framework\TestCase;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class SpecWriterFactoryTest extends TestCase
{
    public function testFactoryCanCreateWriter(): void
    {
        $factory = new SpecWriterFactory();

        $this->assertInstanceOf(SpecYamlWriter::class, $factory->basedOnResource('keepenv_laravel.yaml'));
        $this->assertInstanceOf(SpecYamlWriter::class, $factory->basedOnResource('keepenv.yml'));
    }

    public function testFactoryThrowsExceptionWhenWriterIsNotImplemented(): void
    {
        $this->expectException(SpecFactoryException::class);
        $this->expectExceptionMessage('Specification file type [php] is not implemented yet.');

        $factory = new SpecWriterFactory();
        $factory->basedOnResource('keepenv.php');
    }

    public function testFactoryThrowsExceptionWhenWriterIsNotSupported(): void
    {
        $this->expectException(SpecFactoryException::class);
        $this->expectExceptionMessage('Specification file type [csv] is not supported.');

        $factory = new SpecWriterFactory();
        $factory->basedOnResource('keepenv.csv');
    }
}
