<?php

declare(strict_types=1);

namespace Schranz\Search\Integration\Mezzio\Service;

use Psr\Container\NotFoundExceptionInterface;

/**
 * @internal
 */
class SealContainerNotFoundException extends \Exception implements NotFoundExceptionInterface
{
    public function __construct(string $id)
    {
        parent::__construct(
            \sprintf('Service with id "%s" not found', $id),
        );
    }
}
