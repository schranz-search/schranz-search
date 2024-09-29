<?php

declare(strict_types=1);

/*
 * This file is part of the Schranz Search package.
 *
 * (c) Alexander Schranz <alexander@sulu.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Schranz\Search\SEAL\UI\Controller;

use Schranz\Search\SEAL\EngineRegistry;
use Schranz\Search\SEAL\Schema\Schema;
use Schranz\Search\SEAL\Search\Condition;

class SealUiController
{
    public function __construct(private readonly EngineRegistry $engineRegistry)
    {
    }

    public function __invoke(): string
    {
        $parameters = [
            'query' => $_GET['query'] ?? '',
            'page' => (int) ($_GET['page'] ?? 1),
            'limit' => (int) ($_GET['limit'] ?? 10),
            'index' => $_GET['index'] ?? null,
        ];

        $currentEngine = null;
        $currentIndex = null;
        $currentValue = $parameters['index'] ?? null;

        $engines = [];
        foreach ($this->engineRegistry->getEngines() as $engineKey => $engine) {
            $currentEngine ??= $engineKey;

            $reflectionClass = new \ReflectionClass($engine);
            $propertyReflection = $reflectionClass->getProperty('schema');
            $propertyReflection->setAccessible(true);
            /** @var Schema $schema */
            $schema = $propertyReflection->getValue($engine);
            $indexes = [];
            foreach ($schema->indexes as $indexKey => $index) {
                $currentIndex ??= $indexKey;

                $value = $engineKey . '-' . $indexKey;
                $active = false;

                if ($value === $currentValue) {
                    $currentEngine = $engineKey;
                    $currentIndex = $indexKey;
                    $active = true;
                }

                $title = \ucfirst($indexKey);

                $indexes[$indexKey] = [
                    'title' => $title,
                    'value' => $value,
                    'url' => '?engine=' . $engineKey . '&index=' . $indexKey,
                    'active' => $active,
                ];
            }

            if ([] !== $indexes) {
                $engines[$engineKey] = [
                    'title' => \ucfirst($engineKey),
                    'url' => '?engine=' . $engineKey,
                    'active' => $engineKey === $currentEngine,
                    'indexes' => $indexes,
                ];
            }
        }

        $result = null;
        $queryTime = 0;
        if ($currentEngine && $currentIndex) {
            $query = (string) $parameters['query'];
            $limit = (int) $parameters['limit'];
            $offset = (int) ((($parameters['page'] < 0 ? 1 : $parameters['page']) - 1) * $limit);

            $engine = $this->engineRegistry->getEngine($currentEngine);
            $searchBuilder = $engine->createSearchBuilder()
                ->addIndex($currentIndex)
                ->limit($limit)
                ->offset($offset);

            if ('' !== $query) {
                $searchBuilder->addFilter(new Condition\SearchCondition($query));
            }

            $queryTime = \microtime(true);
            $result = $searchBuilder->getResult();
            $queryTime = \microtime(true) - $queryTime;
        }

        echo $this->render('view/engines', [
            'engines' => $engines,
            'result' => $result,
            'queryTime' => $queryTime,
            'parameters' => $parameters,
        ]);

        exit;
    }

    /**
     * @param array<string mixed> $attributes
     */
    private function render(string $template, array $attributes = []): string
    {
        \extract($attributes);

        \ob_start();

        include \dirname(__DIR__, 2) . '/templates/' . $template . '.inc.php';

        return \ltrim(\ob_get_clean() ?: '');
    }
}
