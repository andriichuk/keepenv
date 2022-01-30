<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Environment\Writer;

use Andriichuk\KeepEnv\Environment\Writer\Exceptions\EnvFileManagerException;

class EnvFileManager implements EnvFileManagerInterface
{
    private string $filePath;

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }

    public function exists(): bool
    {
        return file_exists($this->filePath);
    }

    public function read(): string
    {
        if (!$this->exists()) {
            throw EnvFileManagerException::fileNotExists($this->filePath);
        }

        $content = file_get_contents($this->filePath);

        if ($content === false) {
            throw EnvFileManagerException::cannotRead($this->filePath);
        }

        return $content;
    }

    public function write(string $content): void
    {
        if (!$this->exists()) {
            throw EnvFileManagerException::fileNotExists($this->filePath);
        }

        if (file_put_contents($this->filePath, $content) === false) {
            throw EnvFileManagerException::cannotWrite($this->filePath);
        }
    }

    public function createIfNotExists(): void
    {
        if (!$this->exists()) {
            if (file_put_contents($this->filePath, '') === false) {
                throw EnvFileManagerException::cannotCreateFile($this->filePath);
            }
        }
    }
}
