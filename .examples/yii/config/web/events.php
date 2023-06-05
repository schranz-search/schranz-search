<?php

declare(strict_types=1);

use App\EventHandler\SetLocaleEventHandler;
use Yiisoft\Yii\Middleware\Event\SetLocaleEvent;

return [
    SetLocaleEvent::class => [[SetLocaleEventHandler::class, 'handle']],
];
