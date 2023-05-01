<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Schranz\Search\SEAL\Adapter\AdapterInterface;
use Schranz\Search\SEAL\EngineInterface;
use Schranz\Search\SEAL\EngineRegistry;
use Symfony\Component\HttpFoundation\Response;

class SearchController extends Controller
{
    private readonly EngineInterface $algoliaEngine;
    private readonly EngineInterface $meilisearchEngine;
    private readonly EngineInterface $elasticsearchEngine;
    private readonly EngineInterface $memoryEngine;
    private readonly EngineInterface $opensearchEngine;
    private readonly EngineInterface $solrEngine;
    private readonly EngineInterface $redisearchEngine;
    private readonly EngineInterface $typesenseEngine;
    private readonly EngineInterface $multiEngine;
    private readonly EngineInterface $readWriteEngine;

    public function __construct(
        private readonly EngineRegistry $engineRegistry,
    ) {
        $this->algoliaEngine = $this->engineRegistry->getEngine('algolia');
        $this->meilisearchEngine = $this->engineRegistry->getEngine('meilisearch');
        $this->elasticsearchEngine = $this->engineRegistry->getEngine('elasticsearch');
        $this->memoryEngine = $this->engineRegistry->getEngine('memory');
        $this->opensearchEngine = $this->engineRegistry->getEngine('opensearch');
        $this->solrEngine = $this->engineRegistry->getEngine('solr');
        $this->redisearchEngine = $this->engineRegistry->getEngine('redisearch');
        $this->typesenseEngine = $this->engineRegistry->getEngine('typesense');
        $this->multiEngine = $this->engineRegistry->getEngine('multi');
        $this->readWriteEngine = $this->engineRegistry->getEngine('read-write');
    }

    public function home(): string
    {
        $engineNames = \implode(', ', \array_keys([...$this->engineRegistry->getEngines()]));

        return
            <<<HTML
            <!doctype html>
            <html>
                <head>
                    <title>Search Engines</title>
                </head>
                <body>
                    <h1>Adapters</h1>
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

                    <div>
                        <strong>Registred Engines</strong>: $engineNames
                    </div>
                </body>
            </html>
            HTML;
    }

    public function algolia(): Response
    {
        $class = $this->getAdapterClass($this->algoliaEngine);

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

    public function meilisearch(): Response
    {
        $class = $this->getAdapterClass($this->meilisearchEngine);

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

    public function elasticsearch(): Response
    {
        $class = $this->getAdapterClass($this->elasticsearchEngine);

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

    public function memory(): Response
    {
        $class = $this->getAdapterClass($this->memoryEngine);

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

    public function opensearch(): Response
    {
        $class = $this->getAdapterClass($this->opensearchEngine);

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

    public function solr(): Response
    {
        $class = $this->getAdapterClass($this->solrEngine);

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

    public function redisearch(): Response
    {
        $class = $this->getAdapterClass($this->redisearchEngine);

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

    public function typesense(): Response
    {
        $class = $this->getAdapterClass($this->typesenseEngine);

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

    public function multi(): Response
    {
        $class = $this->getAdapterClass($this->multiEngine);

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

    public function readWrite(): Response
    {
        $class = $this->getAdapterClass($this->readWriteEngine);

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

    private function getAdapterClass(EngineInterface $engine): string
    {
        $reflection = new \ReflectionClass($engine);
        $propertyReflection = $reflection->getProperty('adapter');
        $propertyReflection->setAccessible(true);

        /** @var AdapterInterface $object */
        $object = $propertyReflection->getValue($engine);

        return $object::class;
    }
}
