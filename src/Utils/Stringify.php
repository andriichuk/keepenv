<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Utils;

use JsonException;

class Stringify
{
    /**
     * @param mixed $value
     * @throws JsonException
     */
    public function format($value): string
    {
        if (is_string($value)) {
            return $value;
        }

        switch (true) {
            case is_bool($value):
                return [
                    true => 'true',
                    false => 'false',
                ][$value];

            case $value === null:
                return 'null';

            default:
                return json_encode($value, JSON_THROW_ON_ERROR);
        }
    }
}
