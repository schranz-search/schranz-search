<?php

declare(strict_types=1);

namespace Schranz\Search\SEAL\Adapter;

use Schranz\Search\SEAL\Search\Result;
use Schranz\Search\SEAL\Search\Search;

interface SearcherInterface
{
    public function search(Search $search): Result;
}
