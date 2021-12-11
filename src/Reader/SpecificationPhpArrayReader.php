<?php

declare(strict_types=1);

namespace Andriichuk\Enviro\Reader;

class SpecificationPhpArrayReader implements SpecificationReaderInterface
{
    private array $specification;

    public function __construct(array $specification)
    {
        $this->specification = $specification;
    }

    public function read(string $environment): array
    {
        $common = $this->specification['common'] ?? [];
        $environmentSpecification = $this->specification[$environment] ?? null;

        if ($environmentSpecification === null) {
            throw new \InvalidArgumentException("Environment with the name `{$environment}` is not exists.");
        }

        return array_replace_recursive(
            $common,
            $environmentSpecification,
        );
    }
}
