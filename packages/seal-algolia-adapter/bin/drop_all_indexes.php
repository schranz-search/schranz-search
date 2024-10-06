#!/usr/bin/env php
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

// https://github.com/schranz-search/schranz-search/issues/82

/** @internal */
require_once \dirname(__DIR__) . '/vendor/autoload.php';

if (!isset($_ENV['ALGOLIA_DSN'])) {
    if (!\file_exists(\dirname(__DIR__) . '/phpunit.xml')) {
        throw new \Exception('phpunit.xml not found');
    }

    $data = \file_get_contents(\dirname(__DIR__) . '/phpunit.xml');

    $xml = \simplexml_load_string($data);

    $algoliaDsn = $xml->xpath('//env[@name="ALGOLIA_DSN"]')[0]['value']->__toString();
}

$_ENV['ALGOLIA_DSN'] = $algoliaDsn;

$client = \Schranz\Search\SEAL\Adapter\Algolia\Tests\ClientHelper::getClient();

$return = 0;

$retryIndexes = [];
foreach ($client->listIndices()['items'] as $key => $value) {
    echo 'Delete ... ' . $value['name'] . \PHP_EOL;

    try {
        $client->deleteIndex($value['name']);
    } catch (\Exception) {
        $retryIndexes[$key] = $value;
        echo 'Retry later ... ' . $value['name'] . \PHP_EOL;
    }
}

foreach ($retryIndexes as $key => $value) {
    echo 'Delete ... ' . $value['name'] . \PHP_EOL;

    try {
        $client->deleteIndex($value['name']);
    } catch (\Exception) {
        echo 'Errored ... ' . $value['name'] . \PHP_EOL;
        $return = 1;
    }
}

exit($return);
