<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Environment\Writer;

use Andriichuk\KeepEnv\Environment\Writer\Exceptions\EnvFileWriterException;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class EnvFileWriter implements EnvWriterInterface
{
    private EnvFileManagerInterface $fileManager;
    private static ?string $content;

    public function __construct(EnvFileManagerInterface $fileManager)
    {
        $this->fileManager = $fileManager;
        self::$content = null;
    }

    private function content(): string
    {
        if (self::$content !== null) {
            return self::$content;
        }

        self::$content = $this->fileManager->read();

        return self::$content;
    }

    private function write(string $content): void
    {
        $this->fileManager->write($content);
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
            throw EnvFileWriterException::keyAlreadyDefined($key);
        }

        $this->write($this->content() . $this->prepareLine($key, $value));
    }

    public function addBatch(array $variables): void
    {
        $batch = '';

        foreach ($variables as $key => $value) {
            $batch .= $this->prepareLine($key, $value);
        }

        $this->write($batch);
    }

    private function prepareLine(string $key, string $value): string
    {
        $value = $this->quote($value);

        return "$key=$value" . PHP_EOL;
    }

    public function has(string $key): bool
    {
        return preg_match("#^($key=([^\n]+)?)#miu", $this->content()) === 1;
    }

    public function update(string $key, string $value): void
    {
        $value = $this->quote($value);
        $newContent = preg_replace(
            "#^$key=([^\n]+)?#miu",
            "$key=$value",
            $this->content(),
        );

        $this->write($newContent);
    }

    private function quote(string $value): string
    {
        return mb_strpos($value, ' ') !== false
            ? "\"$value\""
            : $value;
    }
}
