<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Validation\Rules;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
interface RulesRegistryInterface
{
    public function add(RuleInterface $rule): void;

    public function get(string $alias): RuleInterface;
}
