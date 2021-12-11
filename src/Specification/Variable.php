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

    public function __construct(string $name, string $description, $default, array $rules)
    {
        $this->name = $name;
        $this->description = $description;
        $this->default = $default;
        $this->rules = $rules;
    }
}
