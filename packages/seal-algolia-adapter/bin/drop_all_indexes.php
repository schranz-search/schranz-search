#!/usr/bin/env php
<?php

// internal script to cleanup old algolia indices because of
// https://github.com/schranz-search/schranz-search/issues/82

/** @internal */

require_once dirname(__DIR__) . '/vendor/autoload.php';

if (!isset($_ENV['ALGOLIA_APPLICATION_ID'])) {
    if (!file_exists(dirname(__DIR__) . '/phpunit.xml')) {
        throw new \Exception('phpunit.xml not found');
    }

    $data = file_get_contents(dirname(__DIR__) . '/phpunit.xml');

    $xml = simplexml_load_string($data);

    $applicationId = $xml->xpath('//env[@name="ALGOLIA_APPLICATION_ID"]')[0]['value']->__toString();
    $adminId = $xml->xpath('//env[@name="ALGOLIA_ADMIN_API_KEY"]')[0]['value']->__toString();
}

$_ENV['ALGOLIA_APPLICATION_ID'] = $applicationId;
$_ENV['ALGOLIA_ADMIN_API_KEY'] = $adminId;

$client = \Schranz\Search\SEAL\Adapter\Algolia\Tests\ClientHelper::getClient();

$return = 0;
foreach ($client->listIndices()['items'] as $key => $value) {
    echo 'Delete ... ' . ($value['name']) . PHP_EOL;

    try {
        $client->initIndex($value['name'])
            ->delete();
    } catch (\Exception $e) {
        echo 'Errored ... ' . ($value['name']) . PHP_EOL;
        $return = 1;
    }
}

exit($return);
