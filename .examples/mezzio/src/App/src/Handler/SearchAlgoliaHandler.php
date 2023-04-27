<?php

declare(strict_types=1);

namespace App\Handler;

use App\Helper\AdapterClassHelper;
use Laminas\Diactoros\Response\HtmlResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Schranz\Search\SEAL\Engine;
use Schranz\Search\SEAL\EngineRegistry;

class SearchAlgoliaHandler implements RequestHandlerInterface
{
    private readonly Engine $algoliaEngine;

    public function __construct(
        private readonly EngineRegistry $engineRegistry,
    ) {
        $this->algoliaEngine = $this->engineRegistry->getEngine('algolia');
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $class = AdapterClassHelper::getAdapterClass($this->algoliaEngine);

        return new HtmlResponse(
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
}
