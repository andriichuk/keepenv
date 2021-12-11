<?php

declare(strict_types=1);

namespace Andriichuk\EnvValidator;

class EnvSpecReader implements EnvSpecReaderInterface
{
    private array $specification;

    public function __construct(array $specification)
    {
        $this->specification = $specification;
    }

    public function read(): array
    {
        return $this->specification;
    }
}
