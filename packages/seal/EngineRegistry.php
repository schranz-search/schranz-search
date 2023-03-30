<?php

declare(strict_types=1);

namespace Schranz\Search\SEAL;

final class EngineRegistry
{
    /**
     * @var array<string, Engine>
     */
    private array $engines;

    /**
     * @param iterable<string, Engine> $engines
     */
    public function __construct(
        iterable $engines,
    ) {
        $this->engines = [...$engines];
    }

    /**
     * @return iterable<string, Engine>
     */
    public function getEngines(): iterable
    {
        return $this->engines;
    }

    public function getEngine(string $name): Engine
    {
        if (!isset($this->engines[$name])) {
            throw new \InvalidArgumentException(
                'Unknown Search engine: "' . $name . '" available engines are "' . \implode('", "', \array_keys($this->engines)) . '".',
            );
        }

        return $this->engines[$name];
    }
}
