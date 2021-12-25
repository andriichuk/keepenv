<?php

declare(strict_types=1);

namespace Andriichuk\Enviro\Writer\Env;

class EnvFileWriter
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

    private function write(string $content): void
    {
        if (file_put_contents($this->filePath, $content) === false) {
            throw new \RuntimeException('Error on write to file');
        }

        self::$content = $content;
    }

    public function add(string $key, string $value)
    {
        if ($this->has($key)) {
            throw new \RuntimeException("$key is already defined.");
        }

        $this->write($this->content() . "$key=$value");
    }

    public function has(string $key): bool
    {
        return preg_match("#^($key=([^\n]+)?)#miu", $this->content()) === 1;
    }

    public function change()
    {

    }

    public function remove()
    {

    }
}
