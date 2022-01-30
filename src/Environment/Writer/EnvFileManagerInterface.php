<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Environment\Writer;

interface EnvFileManagerInterface
{
    public function exists(): bool;

    public function read(): string;

    public function write(string $content): void;

    public function createIfNotExists(): void;
}
