<?php

declare(strict_types=1);

namespace Andriichuk\Enviro\Reader\Env;

class EnvReader
{
    private string $filePath;
    private static ?string $content = null;

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }

    private function content(): string
    {
        if (self::$content !== null) {
            return self::$content;
        }

        if (!is_file($this->filePath)) {
            throw new \InvalidArgumentException('File does not exists.');
        }

        $content = file_get_contents($this->filePath);

        if ($content === false) {
            throw new \RuntimeException('Cannot read the env file.');
        }

        self::$content = $content;

        return self::$content;
    }

    public function readWithComment(string $key)
    {
        $this->content();
    }
}
