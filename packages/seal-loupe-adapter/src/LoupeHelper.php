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

namespace Schranz\Search\SEAL\Adapter\Loupe;

use Loupe\Loupe\Configuration;
use Loupe\Loupe\Loupe;
use Loupe\Loupe\LoupeFactory;
use Schranz\Search\SEAL\Schema\Index;

/**
 * @experimental
 */
final class LoupeHelper
{
    public const SEPERATOR = '_';
    public const SOURCE_FIELD = 'l_source'; // fields are not allowed to begin with `_`

    /**
     * @var Loupe[]
     */
    private array $loupe = [];

    private readonly string $directory;

    public function __construct(
        private readonly LoupeFactory $loupeFactory,
        string $directory,
    ) {
        $this->directory = '' !== $directory ? (\rtrim($directory, '/') . '/') : '';
    }

    public function getLoupe(Index $index): Loupe
    {
        if (!isset($this->loupe[$index->name])) {
            $this->loupe[$index->name] = $this->createLoupe($index);
        }

        return $this->loupe[$index->name];
    }

    public function existIndex(Index $index): bool
    {
        return \file_exists($this->getIndexFile($index));
    }

    public function dropIndex(Index $index): void
    {
        if ($this->existIndex($index)) {
            \unlink($this->getIndexFile($index));
            \unlink($this->getConfigurationFile($index));
        }
    }

    public function createIndex(Index $index): void
    {
        if (!\file_exists($this->directory)) {
            \mkdir($this->directory, recursive: true);
        }

        // dumping the configuration allows us to search and index without knowing the configuration
        // this way when a similar class like this would be part of loupe only the createIndex method
        // would require then to know the configuration
        $configuration = $this->createConfiguration($index);
        \file_put_contents($this->getConfigurationFile($index), \serialize($configuration));
        \touch($this->getIndexFile($index));
        $this->loupe[$index->name] = $this->createLoupe($index, $configuration);
    }

    public function reset(): void
    {
        $this->loupe = [];
    }

    private function createLoupe(Index $index, ?Configuration $configuration = null): Loupe
    {
        if (!$configuration instanceof Configuration) {
            $configurationFile = $this->getConfigurationFile($index);

            if (!\file_exists($configurationFile)) {
                throw new \LogicException('Configuration need to exist before creating Loupe instance.');
            }

            /** @var string $configurationContent */
            $configurationContent = \file_get_contents($configurationFile);

            /** @var Configuration $configuration */
            $configuration = \unserialize($configurationContent);
        }

        if ('' === $this->directory) {
            return $this->loupeFactory->createInMemory($configuration);
        }

        return $this->loupeFactory->create($this->getIndexFile($index), $configuration);
    }

    private function createConfiguration(Index $index): Configuration
    {
        return Configuration::create()
            ->withPrimaryKey($index->getIdentifierField()->name)
            ->withSearchableAttributes(\array_map(fn (string $field) => \str_replace('.', self::SEPERATOR, $field), $index->searchableFields))
            ->withFilterableAttributes(\array_map(fn (string $field) => \str_replace('.', self::SEPERATOR, $field), $index->filterableFields))
            ->withSortableAttributes(\array_map(fn (string $field) => \str_replace('.', self::SEPERATOR, $field), $index->sortableFields));
    }

    private function getIndexFile(Index $index): string
    {
        return $this->directory . $index->name . '.db';
    }

    private function getConfigurationFile(Index $index): string
    {
        return $this->directory . $index->name . '.loupe';
    }
}
