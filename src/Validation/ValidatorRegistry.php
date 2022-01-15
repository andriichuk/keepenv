<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Validation;

use OutOfRangeException;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class ValidatorRegistry implements ValidatorRegistryInterface
{
    /**
     * @var array<ValidatorInterface>
     */
    private array $validators = [];

    public static function default(): self
    {
        $validatorRegistry = new self();
        $validatorRegistry->add(new NumericValidator());
        $validatorRegistry->add(new EmailValidator());
        $validatorRegistry->add(new EnumValidator());
        $validatorRegistry->add(new EqualsValidator());
        $validatorRegistry->add(new RequiredValidator());

        return $validatorRegistry;
    }

    public function add(ValidatorInterface $validator): void
    {
        $this->validators[$validator->alias()] = $validator;
    }

    public function get(string $alias): ValidatorInterface
    {
        if (!isset($this->validators[$alias])) {
            throw new OutOfRangeException("Undefined validator with alias `{$alias}`.");
        }

        return $this->validators[$alias];
    }
}
