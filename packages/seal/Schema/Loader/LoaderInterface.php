<?php

declare(strict_types=1);

namespace Schranz\Search\SEAL\Schema\Loader;

use Schranz\Search\SEAL\Schema\Schema;

interface LoaderInterface
{
    public function load(): Schema;
}
