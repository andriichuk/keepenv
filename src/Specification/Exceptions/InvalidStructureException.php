<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Specification\Exceptions;

use RuntimeException;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class InvalidStructureException extends RuntimeException
{
    public static function unsupportedVersion(): self
    {
        return new self('Unsupported version of specification file.');
    }

    public static function missingVersion(): self
    {
        return self::missingField('version');
    }

    public static function missingEnvironments(): self
    {
        return self::missingField('environments');
    }

    private static function missingField(string $field): self
    {
        return new self("The `$field` field is required.");
    }

    public static function invalidOrEmptyEnvironments(): self
    {
        return new self('The `environments` field is invalid or empty.');
    }

    public static function missingVariables(string $environment): self
    {
        return new self("The `$environment` environment has no variables. Please define them or remove environment definition.");
    }

    public static function extendsEnvironmentNotFound(string $environment): self
    {
        return new self("No environment found with name `$environment`.");
    }

    public static function extendsEnvNameIsNotString(): self
    {
        return new self('Environment name in `extends` field must be a string.');
    }

    public static function extendsFromItself(): self
    {
        return new self("You cannot extends from itself. Please remove `extends` field or define different environment.");
    }

    public static function nestedExtends(): self
    {
        return new self("Nested extending is not supported yet. You can extend only from one parent.");
    }

    public static function emptyOrInvalidVariableDefinition(): self
    {
        return new self("Variable definition is empty or invalid.");
    }

    public static function invalidVariableDefinition(string $reason): self
    {
        return new self("Invalid variable definition. " . $reason);
    }
}
