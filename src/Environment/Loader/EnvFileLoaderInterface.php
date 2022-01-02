<?php

declare(strict_types=1);

namespace Andriichuk\Enviro\Environment\Loader;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
interface EnvFileLoaderInterface
{
    public function load(array $paths): array;
}
