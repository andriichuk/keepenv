<?php

declare(strict_types=1);

namespace Andriichuk\Enviro\Specification;

use Andriichuk\Enviro\Contracts\ArraySerializable;

class Specification implements ArraySerializable
{
    private string $version;

    /**
     * @var array<EnvSpecification>
     */
    private array $envSpecifications;

    public function add(EnvSpecification $envSpecification): void
    {
        $this->envSpecifications[$envSpecification->getEnvironmentName()] = $envSpecification;
    }

    public function change(string $section, EnvSpecification $envSpecification): void
    {
        if (!isset($this->envSpecifications[$section])) {
            throw new \OutOfRangeException('Specification section is not defined');
        }

        $this->envSpecifications[$section] = $envSpecification;
    }

    public function get(string $section): EnvSpecification
    {
        if (!isset($this->envSpecifications[$section])) {
            throw new \OutOfRangeException('Specification section is not defined');
        }

        return $this->envSpecifications[$section];
    }

    public function toArray(): array
    {
        $specification = [];
        $common = $this->get('common')->toArray();

        foreach ($this->envSpecifications as $envSpecification) {
            $specification[$envSpecification->getEnvironmentName()] = $envSpecification->getEnvironmentName() === 'common'
                ? $common
                : $this->array_diff_assoc_recursive(
                    $envSpecification->toArray(),
                    $common,
                );;
        }

        return $specification;
    }

    function array_diff_assoc_recursive($array1, $array2) {
        $difference=array();
        foreach($array1 as $key => $value) {
            if( is_array($value) ) {
                if( !isset($array2[$key]) || !is_array($array2[$key]) ) {
                    $difference[$key] = $value;
                } else {
                    $new_diff = $this->array_diff_assoc_recursive($value, $array2[$key]);
                    if( !empty($new_diff) )
                        $difference[$key] = $new_diff;
                }
            } else if( !array_key_exists($key,$array2) || $array2[$key] !== $value ) {
                $difference[$key] = $value;
            }
        }
        return $difference;
    }
}
