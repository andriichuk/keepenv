<?php

declare(strict_types=1);

namespace Andriichuk\EnvValidator;

class EnvVariable
{
    public string $name;
    public string $description;

    /**
     * @var mixed
     */
    public $default;
    public array $rules = [];
}
