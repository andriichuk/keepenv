<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Validation\Rules;

use Andriichuk\KeepEnv\Validation\Exceptions\RuleOptionsException;
use Andriichuk\KeepEnv\Validation\RuleInterface;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class StringRule implements RuleInterface
{
    public function alias(): string
    {
        return 'string';
    }

    public function acceptsFalseOption(): bool
    {
        return false;
    }

    public function message(array $placeholders): string
    {
        if (isset($placeholders['min'], $placeholders['max'])) {
            return sprintf(
                'The value length must be between %d and %d.',
                (int) $placeholders['min'],
                (int) $placeholders['max'],
            );
        }

        if (isset($placeholders['min'])) {
            return sprintf(
                'The value length must be greater or equal %d.',
                (int) $placeholders['min'],
            );
        }

        if (isset($placeholders['max'])) {
            return sprintf(
                'The value length must be lower or equal %d.',
                (int) $placeholders['max'],
            );
        }

        return 'The value must be a string.';
    }

    /**
     * @inheritDoc
     */
    public function validate($value, $options): bool
    {
        if (is_bool($options) && $options) {
            return is_string($value);
        }

        if (!is_string($value)) {
            return false;
        }

        if (!is_array($options)) {
            throw RuleOptionsException::invalidStringOptions();
        }

        $length = mb_strlen($value);
        /** @var int|null $min */
        $min = isset($options['min']) ? (int) $options['min'] : null;
        /** @var int|null $max */
        $max = isset($options['max']) ? (int) $options['max'] : null;

        if ($min === null && $max === null) {
            throw RuleOptionsException::invalidStringOptions();
        }

        if (isset($min, $max) && $min > $max) {
            throw RuleOptionsException::invalidStringRanges();
        }

        if ($min !== null && $max === null) {
            return $length >= $min;
        }

        if ($max !== null && $min === null) {
            return $length <= $max;
        }

        return $length >= $min && $length <= $max;
    }
}
