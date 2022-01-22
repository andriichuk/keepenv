<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Application\Command\Utils;

use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class CommandHeader
{
    private SymfonyStyle $io;

    public function __construct(SymfonyStyle $io)
    {
        $this->io = $io;
    }

    public function display(string $title, string $envName, array $envFiles, string $specFile): void
    {
        $this->io->title($title);

        $this->io->listing([
            "Environment name: <info>$envName</info>",
            "DotEnv file paths: [{$this->formatEnvFilePaths($envFiles)}]",
            "Specification file path: <info>{$this->formatSpecPath($specFile)}</info>",
        ]);
    }

    private function formatEnvFilePaths(array $envFiles): string
    {
        $formatted = array_map(static function (string $path): string {
            if (!file_exists($path)) {
                return "<error>$path</error>";
            }

            $absolutePath = (string) realpath($path);

            return "<info>$absolutePath</info>";
        }, $envFiles);

        return implode(', ', $formatted);
    }

    private function formatSpecPath(string $path): string
    {
        if (!file_exists($path)) {
            return "<error>$path</error>";
        }

        $absolutePath = (string) realpath($path);

        return "<info>$absolutePath</info>";
    }
}
