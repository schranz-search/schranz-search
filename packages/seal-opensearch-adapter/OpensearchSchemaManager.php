<?php

namespace Schranz\Search\SEAL\Adapter\Opensearch;

use OpenSearch\Client;
use Schranz\Search\SEAL\Adapter\SchemaManagerInterface;
use Schranz\Search\SEAL\Schema\Field;
use Schranz\Search\SEAL\Schema\Index;

final class OpensearchSchemaManager implements SchemaManagerInterface
{
    public function __construct(
        private readonly Client $client,
    ) {
    }

    public function existIndex(Index $index): bool
    {
        return $this->client->indices()->exists([
            'index' => $index->name,
        ]);
    }

    public function dropIndex(Index $index): void
    {
         $this->client->indices()->delete([
            'index' => $index->name,
        ]);
    }

    public function createIndex(Index $index): void
    {
        $properties = $this->createPropertiesMapping($index->fields);

        $this->client->indices()->create([
            'index' => $index->name,
            'body' => [
                'mappings' => [
                    'properties' => $properties,
                ],
            ],
        ]);
    }

    /**
     * @param Field\AbstractField[] $fields
     *
     * @return array<string, mixed>
     */
    private function createPropertiesMapping(array $fields): array
    {
        $properties = [];

        foreach ($fields as $name => $field) {
            match (true) {
                $field instanceof Field\IdentifierField => $properties[$name] = [
                    'type' => 'keyword',
                ],
                $field instanceof Field\TextField => $properties[$name] = [
                    'type' => 'text',
                ],
                $field instanceof Field\BooleanField => $properties[$name] = [
                    'type' => 'boolean',
                ],
                $field instanceof Field\DateTimeField => $properties[$name] = [
                    'type' => 'date',
                ],
                $field instanceof Field\IntegerField => $properties[$name] = [
                    'type' => 'integer',
                ],
                $field instanceof Field\FloatField => $properties[$name] = [
                    'type' => 'float',
                ],
                $field instanceof Field\ObjectField => $properties[$name] = [
                    'type' => 'object',
                    'properties' => $this->createPropertiesMapping($field->fields),
                ],
                $field instanceof Field\TypedField => $properties = \array_replace($properties, $this->createTypedFieldMapping($name, $field)),
                default => throw new \RuntimeException(sprintf('Field type "%s" is not supported.', get_class($field))),
            };
        }

        return $properties;
    }

    /**
     * @return array<string, mixed>
     */
    private function createTypedFieldMapping(string $name, Field\TypedField $field): array
    {
        $properties = [];

        foreach ($field->types as $type => $fields) {
            $properties[$name . '.' . $type] = [
                'type' => 'nested',
                'properties' => $this->createPropertiesMapping($fields),
            ];
        }

        return $properties;
    }
}
