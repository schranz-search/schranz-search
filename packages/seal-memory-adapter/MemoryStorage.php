<?php

namespace Schranz\Search\SEAL\Adapter\Memory;

use Schranz\Search\SEAL\Schema\Index;

/**
 * @internal This class is not part of the public API and may change or remove without notice.
 */
final class MemoryStorage
{
    /**
     * @var Index[]
     */
    private static array $indexes = [];

    /**
     * @var mixed[]
     */
    private static array $documents = [];

    public static function getDocuments(Index $index): array
    {
        if (!array_key_exists($index->name, self::$indexes)) {
            throw new \RuntimeException('Index "' . $index->name . '" does not exist.');
        }

        return self::$documents[$index->name];
    }

    /**
     * @param array<string, mixed> $document
     *
     * @return array<string, mixed>
     */
    public static function save(Index $index, array $document): array
    {
        if (!array_key_exists($index->name, self::$indexes)) {
            throw new \RuntimeException('Index "' . $index->name . '" does not exist.');
        }

        $identifierField = $index->getIdentifierField();

        $document[$identifierField->name] = (string) $document[$identifierField->name]
            ?? uniqid('id-', true);

        self::$documents[$index->name][$document[$identifierField->name]] = $document;

        return $document;
    }

    public static function delete(Index $index, string $identifier): void
    {
        if (!array_key_exists($index->name, self::$indexes)) {
            throw new \RuntimeException('Index "' . $index->name . '" does not exist.');
        }

        unset(self::$documents[$index->name][$identifier]);
    }

    public static function dropIndex(Index $index): void
    {
        unset(MemoryStorage::$indexes[$index->name]);
        unset(MemoryStorage::$documents[$index->name]);
    }

    public static function createIndex(Index $index): void
    {
        MemoryStorage::$indexes[$index->name] = $index;
    }

    public static function existIndex(Index $index): bool
    {
        return array_key_exists($index->name, MemoryStorage::$indexes);
    }
}
