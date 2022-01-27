<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Validation\Rules;

use Andriichuk\KeepEnv\Validation\Exceptions\RuleOptionsException;
use Andriichuk\KeepEnv\Validation\RuleInterface;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class EnumRule implements RuleInterface
{
    public function alias(): string
    {
        return 'enum';
    }

    public function acceptsFalseOption(): bool
    {
        return false;
    }

    public function message(array $placeholders): string
    {
        $cases = array_map(
            /** @param mixed $case */
            static fn ($case) => (string) $case,
            (array) ($placeholders['cases'] ?? [])
        );

        return sprintf(
            'The value must match one of the values: %s.',
            implode(', ', $cases),
        );
    }

    /**
     * @inheritDoc
     */
    public function validate($value, $options): bool
    {
        if (empty($options) || !is_array($options)) {
            throw RuleOptionsException::invalidCasesForEnum();
        }

        return in_array($value, $options);
    }
}
