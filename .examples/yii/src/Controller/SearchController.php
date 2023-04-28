<?php

declare(strict_types=1);

namespace App\Controller;

use Psr\Http\Message\ResponseInterface;
use Schranz\Search\SEAL\Adapter\AdapterInterface;
use Schranz\Search\SEAL\Engine;
use Schranz\Search\SEAL\EngineRegistry;
use Yiisoft\DataResponse\DataResponseFactoryInterface;

final class SearchController
{
    private readonly Engine $algoliaEngine;
    private readonly Engine $meilisearchEngine;
    private readonly Engine $elasticsearchEngine;
    private readonly Engine $memoryEngine;
    private readonly Engine $opensearchEngine;
    private readonly Engine $solrEngine;
    private readonly Engine $redisearchEngine;
    private readonly Engine $typesenseEngine;
    private readonly Engine $multiEngine;
    private readonly Engine $readWriteEngine;

    public function __construct(
        private readonly DataResponseFactoryInterface $responseFactory,
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

    public function home(): ResponseInterface
    {
        $engineNames = \implode(', ', \array_keys([...$this->engineRegistry->getEngines()]));

        return $this->responseFactory->createResponse(
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
            HTML
        );
    }

    public function algolia(): ResponseInterface
    {
        $class = $this->getAdapterClass($this->algoliaEngine);

        return $this->responseFactory->createResponse(
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

    public function meilisearch(): ResponseInterface
    {
        $class = $this->getAdapterClass($this->meilisearchEngine);

        return $this->responseFactory->createResponse(
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

    public function elasticsearch(): ResponseInterface
    {
        $class = $this->getAdapterClass($this->elasticsearchEngine);

        return $this->responseFactory->createResponse(
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

    public function memory(): ResponseInterface
    {
        $class = $this->getAdapterClass($this->memoryEngine);

        return $this->responseFactory->createResponse(
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

    public function opensearch(): ResponseInterface
    {
        $class = $this->getAdapterClass($this->opensearchEngine);

        return $this->responseFactory->createResponse(
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

    public function solr(): ResponseInterface
    {
        $class = $this->getAdapterClass($this->solrEngine);

        return $this->responseFactory->createResponse(
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

    public function redisearch(): ResponseInterface
    {
        $class = $this->getAdapterClass($this->redisearchEngine);

        return $this->responseFactory->createResponse(
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

    public function typesense(): ResponseInterface
    {
        $class = $this->getAdapterClass($this->typesenseEngine);

        return $this->responseFactory->createResponse(
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

    public function multi(): ResponseInterface
    {
        $class = $this->getAdapterClass($this->multiEngine);

        return $this->responseFactory->createResponse(
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

    public function readWrite(): ResponseInterface
    {
        $class = $this->getAdapterClass($this->readWriteEngine);

        return $this->responseFactory->createResponse(
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

    private function getAdapterClass(Engine $engine): string
    {
        $reflection = new \ReflectionClass($engine);
        $propertyReflection = $reflection->getProperty('adapter');
        $propertyReflection->setAccessible(true);

        /** @var AdapterInterface $object */
        $object = $propertyReflection->getValue($engine);

        return $object::class;
    }
}
