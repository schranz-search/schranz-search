<?php

declare(strict_types=1);

namespace App\Search;

use Schranz\Search\SEAL\Reindex\ReindexProviderInterface;

class BlogReindexProvider implements ReindexProviderInterface
{
    public function total(): int|null
    {
        return 2400;
    }

    public function provide(): \Generator
    {
        $total = $this->total();

        for ($i = 1; $i <= $total; $i++) {
            yield [
                'id' => $i,
                'title' => 'Title ' . $i,
                'description' => 'Description ' . $i,
            ];
        }
    }

    public static function getIndex(): string
    {
        return 'blog';
    }
}
