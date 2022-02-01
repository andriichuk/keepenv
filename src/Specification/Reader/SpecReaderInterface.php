<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Specification\Reader;

use Andriichuk\KeepEnv\Specification\Specification;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
interface SpecReaderInterface
{
    public function read(string $source): Specification;
}
