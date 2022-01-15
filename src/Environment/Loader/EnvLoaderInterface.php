<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Environment\Loader;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
interface EnvLoaderInterface
{
    public function load(array $paths, bool $overrideExisting): array;
}
