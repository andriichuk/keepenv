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

    public function save(string $key, string $value): void
    {
        if ($this->has($key)) {
            $this->update($key, $value);
        } else {
            $this->add($key, $value);
        }
    }

    public function add(string $key, string $value): void
    {
        if ($this->has($key)) {
            throw new \RuntimeException("$key is already defined.");
        }

        $this->write($this->content() . "$key=$value\r\n");
    }

    public function has(string $key): bool
    {
        return preg_match("#^($key=([^\n]+)?)#miu", $this->content()) === 1;
    }

    public function update(string $key, string $value): void
    {
        $content = preg_replace(
            "#^$key=([^\n]+)?#miu",
            "$key=\"$value\"" . PHP_EOL,
            $this->content(),
        );

        $this->write($content);
    }

    public function remove(string $key): void
    {
        $this->write(preg_replace("#([\r\n]+)?^$key=([^\n]+)?#miu", '', $this->content()));
    }
}
