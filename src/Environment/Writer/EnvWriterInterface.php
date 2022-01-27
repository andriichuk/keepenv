<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Environment\Writer;

interface EnvWriterInterface
{
    public function save(string $key, string $value): void;

    public function add(string $key, string $value): void;

    public function has(string $key): bool;

    public function update(string $key, string $value): void;
}
