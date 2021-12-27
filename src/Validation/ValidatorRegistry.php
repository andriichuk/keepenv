<?php

declare(strict_types=1);

namespace Andriichuk\Enviro\Validation;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
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
