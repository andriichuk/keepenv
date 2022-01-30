<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Environment\Writer;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
interface EnvFileManagerInterface
{
    public function exists(): bool;

    public function read(): string;

    public function write(string $content): void;

    public function createIfNotExists(): void;
}
