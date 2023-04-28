<?php

declare(strict_types=1);

namespace App\Controller;

use Psr\Http\Message\ResponseInterface;
use Schranz\Search\SEAL\Adapter\AdapterFactory;
use Schranz\Search\SEAL\Adapter\Algolia\AlgoliaAdapterFactory;
use Schranz\Search\SEAL\EngineRegistry;
use Yiisoft\Yii\View\ViewRenderer;

final class SiteController
{
    public function __construct(private ViewRenderer $viewRenderer, private EngineRegistry $engineRegistry)
    {
        $this->viewRenderer = $viewRenderer->withControllerName('site');
    }

    public function index(): ResponseInterface
    {
        return $this->viewRenderer->render('index');
    }
}
