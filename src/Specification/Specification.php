<?php

declare(strict_types=1);

namespace Andriichuk\Enviro\Specification;

use Andriichuk\Enviro\Contracts\ArraySerializable;

class Specification implements ArraySerializable
{
    private const VERSION = '1.0';
    private const COMMON_ENV_NAME = 'common';

    private string $version;

    /**
     * @var array<EnvVariables>
     */
    private array $envVariables;

    public function __construct(string $version)
    {
        if (!version_compare($version, self::VERSION, '==')) {
            throw new \InvalidArgumentException('Unsupported version');
        }

        $this->version = $version;
        $this->envVariables = [];
    }

    public function add(EnvVariables $envVariables): void
    {
        $this->envVariables[$envVariables->getEnvName()] = $envVariables;
    }

    public function change(string $envName, EnvVariables $envVariables): void
    {
        if (!isset($this->envVariables[$envName])) {
            throw new \OutOfRangeException('Specification section is not defined');
        }

        $this->envVariables[$envName] = $envVariables;
    }

    public function get(string $envName): EnvVariables
    {
        if (!isset($this->envVariables[$envName])) {
            throw new \OutOfRangeException('Specification section is not defined');
        }

        return $this->envVariables[$envName];
    }

    public function toArray(): array
    {
        $envVariables = [];
        $common = $this->get(self::COMMON_ENV_NAME)->toArray();

        foreach ($this->envVariables as $envSpecification) {
            $envVariables[$envSpecification->getEnvName()] = $envSpecification->getEnvName() === self::COMMON_ENV_NAME
                ? $common
                : $this->arrayDiffAssocRecursive(
                    $envSpecification->toArray(),
                    $common,
                );
        }

        return [
            'version' => $this->version,
            'environments' => $envVariables,
        ];
    }

    private function arrayDiffAssocRecursive(array $array1, array $array2): array
    {
        $difference = array();

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
            } else if (!array_key_exists($key, $array2) || $array2[$key] !== $value) {
                $difference[$key] = $value;
            }
        }

        return $difference;
    }
}
