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

    public function save(string $key, string $value, bool $export = false): void
    {
        if ($this->has($key)) {
            $this->update($key, $value, $export);
        } else {
            $this->add($key, $value, $export);
        }
    }

    public function add(string $key, string $value, bool $export = false): void
    {
        if ($this->has($key)) {
            throw EnvFileWriterException::keyAlreadyDefined($key);
        }

        $this->write($this->content() . $this->prepareNewLine($key, $value, $export));
    }

    public function addBatch(array $variables): void
    {
        $batch = '';

        foreach ($variables as $key => ['value' => $value, 'export' => $export]) {
            $batch .= $this->prepareNewLine($key, (string) $value, (bool) $export);
        }

        $this->write($batch);
    }

    public function has(string $key): bool
    {
        return preg_match("#^(export[\s]+)?($key=([^\n]+)?)#miu", $this->content()) === 1;
    }

    public function update(string $key, string $value, bool $export = false): void
    {
        $newContent = preg_replace(
            "#^(export[\s]+)?$key=([^\n]+)?#miu",
            $this->prepareLine($key, $value, $export),
            $this->content(),
        );

        $this->write($newContent);
    }

    private function prepareNewLine(string $key, string $value, bool $export = false): string
    {
        return $this->prepareLine($key, $value, $export) . PHP_EOL;
    }

    private function prepareLine(string $key, string $value, bool $export = false): string
    {
        $value = mb_strpos($value, ' ') !== false ? "\"$value\"" : $value;

        return ($export ? 'export ' : '') . "$key=$value";
    }
}
