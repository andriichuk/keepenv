<?php

declare(strict_types=1);

namespace Andriichuk\EnvValidator\Validation;

class ValidatorRegistry implements ValidatorRegistryInterface
{
    /**
     * @var array<ValidatorInterface>
     */
    private array $validators = [];

    public function add(ValidatorInterface $validator): void
    {
        $this->validators[$validator->alias()] = $validator;
    }

    public function get(string $alias): ValidatorInterface
    {
        if (!isset($this->validators[$alias])) {
            throw new \OutOfRangeException("Undefined validator with alias `{$alias}`.");
        }

        return $this->validators[$alias];
    }
}
