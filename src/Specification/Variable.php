<?php

declare(strict_types=1);

namespace Andriichuk\Enviro\Specification;

use Andriichuk\Enviro\Contracts\ArraySerializable;

class Variable implements ArraySerializable
{
    public string $name;
    public string $description;
    public ?array $rules;

    /**
     * @var mixed
     */
    public $default;

    /**
     * @param mixed $default
     */
    public function __construct(string $name, string $description, ?array $rules = null, $default = null)
    {
        $this->name = $name;
        $this->description = $description;
        $this->default = $default;
        $this->rules = $rules;
    }

    public function toArray(): array
    {
        return array_filter([
            'description' => $this->description,
            'default' => $this->default,
            'rules' => $this->rules,
        ]);
    }
}
