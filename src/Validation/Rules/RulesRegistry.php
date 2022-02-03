<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Validation\Rules;

use OutOfRangeException;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class RulesRegistry implements RulesRegistryInterface
{
    /**
     * @var array<RuleInterface>
     */
    private array $rules = [];

    public static function default(): self
    {
        $validatorRegistry = new self();
        $validatorRegistry->add(new StringRule());
        $validatorRegistry->add(new NumericRule());
        $validatorRegistry->add(new BooleanRule());
        $validatorRegistry->add(new EmailRule());
        $validatorRegistry->add(new IpRule());
        $validatorRegistry->add(new EnumRule());
        $validatorRegistry->add(new EqualsRule());
        $validatorRegistry->add(new RequiredRule());

        return $validatorRegistry;
    }

    public function add(RuleInterface $rule): void
    {
        $this->rules[$rule->alias()] = $rule;
    }

    public function get(string $alias): RuleInterface
    {
        if (!isset($this->rules[$alias])) {
            throw new OutOfRangeException("Undefined validation rule with alias `{$alias}`.");
        }

        return $this->rules[$alias];
    }

    /**
     * @param string[] $except
     *
     * @return string[]
     */
    public function listOfAliases(array $except): array
    {
        return array_diff(array_keys($this->rules), $except);
    }
}
