<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Specification;

use Andriichuk\KeepEnv\Contracts\ArraySerializable;
use Andriichuk\KeepEnv\Specification\Exceptions\InvalidStructureException;
use OutOfRangeException;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class Specification implements ArraySerializable
{
    private const VERSION = '1.0';
    private string $version;

    /**
     * @var array<EnvVariables>
     */
    private array $envVariables;

    public function __construct(string $version)
    {
        if (!version_compare($version, self::VERSION, '==')) {
            throw InvalidStructureException::unsupportedVersion();
        }

        $this->version = $version;
        $this->envVariables = [];
    }

    public static function default(): self
    {
        return new self(self::VERSION);
    }

    public function add(EnvVariables $envVariables): void
    {
        $this->envVariables[$envVariables->getEnvName()] = $envVariables;
    }

    public function has(string $envName): bool
    {
        return isset($this->envVariables[$envName]);
    }

    public function get(string $envName): EnvVariables
    {
        if (!$this->has($envName)) {
            throw new OutOfRangeException("Environment with name `$envName` is not defined in the specification.");
        }

        return $this->envVariables[$envName];
    }

    public function toArray(): array
    {
        $envVariablesSerialized = [];

        foreach ($this->envVariables as $envSpecification) {
            $extends = $envSpecification->getExtends();

            if ($extends !== null) {
                if (!isset($this->envVariables[$extends])) {
                    throw new OutOfRangeException("Environment with name `{$extends}` not found.");
                }

                $serialized = $this->arrayDiffAssocRecursive(
                    $envSpecification->toArray(),
                    $this->envVariables[$extends]->toArray(),
                );
            } else {
                $serialized = $envSpecification->toArray();
            }

            $envVariablesSerialized[$envSpecification->getEnvName()] = $serialized;
        }

        return [
            'version' => $this->version,
            'environments' => $envVariablesSerialized,
        ];
    }

    private function arrayDiffAssocRecursive(array $array1, array $array2): array
    {
        $difference = [];

        /**
         * @var string $key
         * @var mixed $value
         */
        foreach ($array1 as $key => $value) {
            if (is_array($value)) {
                if (!isset($array2[$key]) || !is_array($array2[$key])) {
                    $difference[$key] = $value;
                } else {
                    $new_diff = $this->arrayDiffAssocRecursive($value, $array2[$key]);

                    if (!empty($new_diff)) {
                        $difference[$key] = $new_diff;
                    }
                }
            } elseif (!array_key_exists($key, $array2) || $array2[$key] !== $value) {
                $difference[$key] = $value;
            }
        }

        return $difference;
    }
}
