<?php

declare(strict_types=1);

namespace Andriichuk\Enviro\Reader;

use Symfony\Component\Yaml\Yaml;

class SpecificationYamlReader implements SpecificationReaderInterface
{
    private string $sourceFile;

    public function __construct(string $sourceFile)
    {
        $this->sourceFile = $sourceFile;
    }

    public function read(string $environment): array
    {
        $specification = Yaml::parseFile($this->sourceFile);

        $common = $specification['common'] ?? [];
        $environmentSpecification = $specification[$environment] ?? null;

        if ($environmentSpecification === null) {
            throw new \InvalidArgumentException("Environment with the name `{$environment}` is not exists.");
        }

        return array_replace_recursive(
            $common,
            $environmentSpecification,
        );
    }
}
