<?php

declare(strict_types=1);

/*
 * This file is part of the Schranz Search package.
 *
 * (c) Alexander Schranz <alexander@sulu.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Schranz\Search\SEAL\Search\Condition;

class GeoBoundingBoxCondition
{
    /**
     * The order may first be unusally, but it is the same as in common JS libraries like.
     *
     * @see https://docs.mapbox.com/help/glossary/bounding-box/
     * @see https://developers.google.com/maps/documentation/javascript/reference/coordinates#LatLngBounds
     */
    public function __construct(
        public readonly string $field,
        public readonly float $northLatitude, // top
        public readonly float $eastLongitude, // right
        public readonly float $southLatitude, // bottom
        public readonly float $westLongitude, // left
    ) {
    }
}
