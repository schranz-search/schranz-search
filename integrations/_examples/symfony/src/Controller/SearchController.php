<?php

namespace App\Controller;

use Schranz\Search\SEAL\Adapter\AdapterInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchController
{
    #[Route('/', name: 'home')]
    public function index(): Response
    {
        return new Response(
<<<HTML
            <!doctype html>
            <html>
                <head>
                    <title>Search Engines</title>
                </head>
                <body>
                    <ul>
                        <li><a href="/algolia">Algolia</a></li>
                        <li><a href="/elasticsearch">Elasticsearch</a></li>
                        <li><a href="/meilisearch">Meilisearch</a></li>
                        <li><a href="/memory">Memory</a></li>
                        <li><a href="/opensearch">Opensearch</a></li>
                        <li><a href="/redisearch">RediSearch</a></li>
                        <li><a href="/solr">Solr</a></li>
                        <li><a href="/typesense">Typesense</a></li>
                        <li>....</li>
                        <li><a href="/multi">Multi</a></li>
                        <li><a href="/read-write">Read-Write</a></li>
                    </ul>
                </body>
            </html>
HTML
        );
    }

    #[Route('/algolia', name: 'algolia')]
    public function algolia(AdapterInterface $algoliaAdapter): Response
    {
        $class = $algoliaAdapter::class;

        return new Response(
<<<HTML
            <!doctype html>
            <html>
                <head>
                    <title>Algolia</title>
                </head>
                <body>
                    <h1>$class</h1>
                </body>
            </html>
HTML
        );
    }

    #[Route('/meilisearch', name: 'meilisearch')]
    public function meilisearch(AdapterInterface $meilisearchAdapter): Response
    {
        $class = $meilisearchAdapter::class;

        return new Response(
            <<<HTML
            <!doctype html>
            <html>
                <head>
                    <title>Meilisearch</title>
                </head>
                <body>
                    <h1>$class</h1>
                </body>
            </html>
HTML
        );
    }

    #[Route('/elasticsearch', name: 'elasticsearch')]
    public function elasticsearch(AdapterInterface $elasticsearchAdapter): Response
    {
        $class = $elasticsearchAdapter::class;

        return new Response(
<<<HTML
            <!doctype html>
            <html>
                <head>
                    <title>Elasticsearch</title>
                </head>
                <body>
                    <h1>$class</h1>
                </body>
            </html>
HTML
        );
    }

    #[Route('/memory', name: 'memory')]
    public function memory(AdapterInterface $memoryAdapter): Response
    {
        $class = $memoryAdapter::class;

        return new Response(
<<<HTML
            <!doctype html>
            <html>
                <head>
                    <title>Memory</title>
                </head>
                <body>
                    <h1>$class</h1>
                </body>
            </html>
HTML
        );
    }

    #[Route('/opensearch', name: 'opensearch')]
    public function opensearch(AdapterInterface $opensearchAdapter): Response
    {
        $class = $opensearchAdapter::class;

        return new Response(
<<<HTML
            <!doctype html>
            <html>
                <head>
                    <title>Opensearch</title>
                </head>
                <body>
                    <h1>$class</h1>
                </body>
            </html>
HTML
        );
    }

    #[Route('/solr', name: 'solr')]
    public function solr(AdapterInterface $solrAdapter): Response
    {
        $class = $solrAdapter::class;

        return new Response(
<<<HTML
            <!doctype html>
            <html>
                <head>
                    <title>Solr</title>
                </head>
                <body>
                    <h1>$class</h1>
                </body>
            </html>
HTML
        );
    }

    #[Route('/redisearch', name: 'redisearch')]
    public function redisearch(AdapterInterface $redisearchAdapter): Response
    {
        $class = $redisearchAdapter::class;

        return new Response(
<<<HTML
            <!doctype html>
            <html>
                <head>
                    <title>RediSearch</title>
                </head>
                <body>
                    <h1>$class</h1>
                </body>
            </html>
HTML
        );
    }

    #[Route('/typesense', name: 'typesense')]
    public function typesense(AdapterInterface $typesenseAdapter): Response
    {
        $class = $typesenseAdapter::class;

        return new Response(
<<<HTML
            <!doctype html>
            <html>
                <head>
                    <title>Typesense</title>
                </head>
                <body>
                    <h1>$class</h1>
                </body>
            </html>
HTML
        );
    }

    #[Route('/multi', name: 'multi')]
    public function multi(AdapterInterface $multiAdapter): Response
    {
        $class = $multiAdapter::class;

        return new Response(
<<<HTML
            <!doctype html>
            <html>
                <head>
                    <title>Multi</title>
                </head>
                <body>
                    <h1>$class</h1>
                </body>
            </html>
HTML
        );
    }

    #[Route('/read-write', name: 'read_write')]
    public function readWrite(AdapterInterface $readWriteAdapter): Response
    {
        $class = $readWriteAdapter::class;

        return new Response(
<<<HTML
            <!doctype html>
            <html>
                <head>
                    <title>Read-Write</title>
                </head>
                <body>
                    <h1>$class</h1>
                </body>
            </html>
HTML
        );
    }
}
