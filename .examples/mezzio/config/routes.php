<?php

declare(strict_types=1);

use Mezzio\Application;
use Mezzio\MiddlewareFactory;
use Psr\Container\ContainerInterface;

/*
 * FastRoute route configuration
 *
 * @see https://github.com/nikic/FastRoute
 *
 * Setup routes with a single request method:
 *
 * $app->get('/', App\Handler\HomePageHandler::class, 'home');
 * $app->post('/album', App\Handler\AlbumCreateHandler::class, 'album.create');
 * $app->put('/album/{id:\d+}', App\Handler\AlbumUpdateHandler::class, 'album.put');
 * $app->patch('/album/{id:\d+}', App\Handler\AlbumUpdateHandler::class, 'album.patch');
 * $app->delete('/album/{id:\d+}', App\Handler\AlbumDeleteHandler::class, 'album.delete');
 *
 * Or with multiple request methods:
 *
 * $app->route('/contact', App\Handler\ContactHandler::class, ['GET', 'POST', ...], 'contact');
 *
 * Or handling all request methods:
 *
 * $app->route('/contact', App\Handler\ContactHandler::class)->setName('contact');
 *
 * or:
 *
 * $app->route(
 *     '/contact',
 *     App\Handler\ContactHandler::class,
 *     Mezzio\Router\Route::HTTP_METHOD_ANY,
 *     'contact'
 * );
 */

return static function (Application $app, MiddlewareFactory $factory, ContainerInterface $container): void {
    $app->get('/api/ping', App\Handler\PingHandler::class, 'api.ping');

    $app->get('/', App\Handler\SearchHandler::class, 'home');
    $app->get('/algolia', App\Handler\SearchAlgoliaHandler::class, 'algolia');
    $app->get('/elasticsearch', App\Handler\SearchElasticsearchHandler::class, 'elasticsearch');
    $app->get('/meilisearch', App\Handler\SearchMeilisearchHandler::class, 'meilisearch');
    $app->get('/memory', App\Handler\SearchMemoryHandler::class, 'memory');
    $app->get('/multi', App\Handler\SearchMultiHandler::class, 'multi');
    $app->get('/opensearch', App\Handler\SearchOpensearchHandler::class, 'opensearch');
    $app->get('/read-write', App\Handler\SearchReadWriteHandler::class, 'read_write');
    $app->get('/redisearch', App\Handler\SearchRedisearchHandler::class, 'redisearch');
    $app->get('/solr', App\Handler\SearchSolrHandler::class, 'solr');
    $app->get('/typesense', App\Handler\SearchTypesenseHandler::class, 'typesense');
};
