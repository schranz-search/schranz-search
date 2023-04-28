<?php

declare(strict_types=1);

namespace App\ViewInjection;

use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Yii\View\CommonParametersInjectionInterface;

final class TranslatorViewInjection implements CommonParametersInjectionInterface
{
    public function __construct(private TranslatorInterface $translator)
    {
    }

    public function getCommonParameters(): array
    {
        return [
            'translator' => $this->translator,
        ];
    }
}
