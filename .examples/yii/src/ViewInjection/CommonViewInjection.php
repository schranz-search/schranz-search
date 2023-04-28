<?php

declare(strict_types=1);

namespace App\ViewInjection;

use App\ApplicationParameters;
use Yiisoft\Yii\View\CommonParametersInjectionInterface;

final class CommonViewInjection implements CommonParametersInjectionInterface
{
    public function __construct(private ApplicationParameters $applicationParameters)
    {
    }

    public function getCommonParameters(): array
    {
        return [
            'applicationParameters' => $this->applicationParameters,
        ];
    }
}
