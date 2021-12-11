<?php

declare(strict_types=1);

namespace Andriichuk\Enviro\Specification;

class Variable
{
    public string $name;
    public string $description;

    /**
     * @var mixed
     */
    public $default;
    public array $rules = [];
}
