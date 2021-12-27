<?php

declare(strict_types=1);

namespace Andriichuk\Enviro\Reader\Specification;

use Andriichuk\Enviro\Specification\Specification;

/**
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
interface SpecificationReaderInterface
{
    public function read(string $source): Specification;
}
