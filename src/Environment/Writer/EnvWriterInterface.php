<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Environment\Writer;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
interface EnvWriterInterface
{
    public function save(string $key, string $value, bool $export = false): void;

    public function add(string $key, string $value, bool $export = false): void;

    /**
     * @param array<string, array<string, mixed>> $variables
     */
    public function addBatch(array $variables, bool $skipExisting): void;

    public function has(string $key): bool;

    public function update(string $key, string $value, bool $export = false): void;
}
