<?php

declare(strict_types=1);

namespace App\EventHandler;

use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Yii\Middleware\Event\SetLocaleEvent;

final class SetLocaleEventHandler
{
    public function __construct(
        private TranslatorInterface $translator,
    ) {
    }

    public function handle(SetLocaleEvent $event): void
    {
        $this->translator->setLocale($event->getLocale());
    }
}
