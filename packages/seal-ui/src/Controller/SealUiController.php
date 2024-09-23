<?php

namespace Schranz\Search\SEAL\UI\Controller;

use Schranz\Search\SEAL\EngineRegistry;
use Schranz\Search\SEAL\Schema\Schema;
use Schranz\Search\SEAL\Search\Condition;

class SealUiController
{
    public function __construct(private EngineRegistry $engineRegistry)
    {
    }

    public function __invoke(): string
    {
        $parameters = $_GET ?? [];

        $currentEngine = $parameters['engine'] ?? null;
        $currentIndex = $parameters['index'] ?? null;

        $engines = [];
        foreach ($this->engineRegistry->getEngines() as $key => $engine) {
            $currentEngine = $currentEngine ?? $key;

            $reflectionClass = new \ReflectionClass($engine);
            $propertyReflection = $reflectionClass->getProperty('schema');
            $propertyReflection->setAccessible(true);
            /** @var Schema $schema */
            $schema = $propertyReflection->getValue($engine);
            $indexes = [];
            foreach ($schema->indexes as $indexKey => $index) {
                $currentIndex = $currentIndex ?? $indexKey;

                $indexes[$indexKey] = [
                    'title' => \ucfirst($key),
                    'url' => '?engine=' . $key . '&index=' . $indexKey,
                    'active' => $indexKey === $currentIndex,
                ];
            }

            $engines[$key] = [
                'title' => \ucfirst($key),
                'url' => '?engine=' . $key,
                'active' => $key === $currentEngine,
                'indexes' => $indexes,
            ];
        }

        $result = null;
        if ($currentEngine && $currentIndex) {
            $query = $parameters['query'] ?? null;
            $limit = $parameters['limit'] ?? 20;
            $offset = ((($parameters['page'] ?? 1) - 1) * $limit);

            $engine = $this->engineRegistry->getEngine($currentEngine);
            $searchBuilder = $engine->createSearchBuilder()
                ->addIndex($currentIndex)
                ->limit($limit)
                ->offset($offset)
            ;

            if ($query) {
                $searchBuilder->addFilter(new Condition\SearchCondition($query));
            }

            $result = $searchBuilder->getResult();
        }

        echo $this->render('view/engines', [
            'engines' => $engines,
            'result' => $result,
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
