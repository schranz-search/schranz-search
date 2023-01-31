<?php

namespace Schranz\Search\SEAL\Marshaller;

use Schranz\Search\SEAL\Schema\Field;

/**
 * @internal This class currently in discussion to be open for all adapters.
 *
 * The Marshaller will split the typed fields into different subfields and use a `_originalIndex` field to unmarshall it again.
 */
final class Marshaller
{
    public function __construct(
        private readonly bool $dateAsInteger = false,
    ) {}

    /**
     * @param Field\AbstractField[] $fields
     * @param array<string, mixed> $object
     *
     * @return array<string, mixed>
     */
    public function marshall(array $fields, array $document): array
    {
        $rawDocument = [];

        foreach ($fields as $name => $field) {
            if (!\array_key_exists($field->name, $document)) {
                continue;
            }

            if ($field->multiple && !\is_array($document[$field->name])) {
                throw new \RuntimeException('Field "' . $field->name . '" is multiple but value is not an array.');
            }

            match (true) {
                $field instanceof Field\ObjectField => $rawDocument[$name] = $this->marshallObjectFields($document[$field->name], $field),
                $field instanceof Field\TypedField => $rawDocument = \array_replace($rawDocument, $this->marhsallTypedFields($name, $document[$field->name], $field)),
                $field instanceof Field\DateTimeField => $rawDocument[$name] = $this->marshallDateTimeField($document[$field->name], $field),
                default => $rawDocument[$name] = $document[$field->name],
            };
        }

        return $rawDocument;
    }

    /**
     * @return int|string
     */
    private function marshallDateTimeField(?string $value, Field\DateTimeField $field): int|string
    {
        if ($field->multiple) {
            return \array_map(function($value) {
                if ($value !== null && $this->dateAsInteger) {
                    return \strtotime($value);
                }

                return $value;
            }, $value);
        }

        if ($value !== null && $this->dateAsInteger) {
            return \strtotime($value);
        }

        return $value;
    }

    /**
     * @param array<string, mixed> $document
     *
     * @return array<string, mixed>
     */
    private function marshallObjectFields(array $document, Field\ObjectField $field): array
    {
        if (!$field->multiple) {
            return $this->marshall($field->fields, $document);
        }

        $rawDocuments = [];
        foreach ($document as $data) {
            $rawDocuments[] = $this->marshall($field->fields, $data);
        }

        return $rawDocuments;
    }

    /**
     * @param array<string, mixed> $document
     *
     * @return array<string, mixed>
     */
    private function marhsallTypedFields(string $name, array $document, Field\TypedField $field): array
    {
        $rawFields = [];

        if (!$field->multiple) {
            $document = [$document];
        }

        foreach ($document as $originalIndex => $data) {
            /** @var string|null $type */
            $type = $data[$field->typeField] ?? null;
            if ($type === null || !\array_key_exists($type, $field->types)) {
                throw new \RuntimeException('Expected type field "' . $field->typeField . '" not found in document.');
            }

            $typedFields = $field->types[$type];

            $rawData = \array_replace($field->multiple ? [
                '_originalIndex' => $originalIndex,
            ] : [], $this->marshall($typedFields, $data));

            if ($field->multiple) {
                $rawFields[$name][$type][] = $rawData;

                continue;
            }

            $rawFields[$name][$type] = $rawData;
        }

        return $rawFields;
    }

    /**
     * @param Field\AbstractField[] $fields
     * @param array<string, mixed> $raw
     *
     * @return array<string, mixed>
     */
    public function unmarshall(array $fields, array $raw): array
    {
        $document = [];

        foreach ($fields as $name => $field) {
            if (!\array_key_exists($name, $raw) && !$field instanceof Field\TypedField ) {
                continue;
            }

            match (true) {
                $field instanceof Field\ObjectField => $document[$field->name] = $this->unmarshallObjectFields($raw[$name], $field),
                $field instanceof Field\TypedField => $document = \array_replace($document, $this->unmarshallTypedFields($name, $raw, $field)),
                $field instanceof Field\DateTimeField => $document[$name] = $this->unmarshallDateTimeField($raw[$field->name], $field),
                default => $document[$field->name] = $raw[$name] ?? ($field->multiple ? [] : null),
            };
        }

        return $document;
    }

    /**
     * @param array<string, mixed> $document
     *
     * @return array<string, mixed>
     */
    private function unmarshallTypedFields(string $name, array $raw, Field\TypedField $field): array
    {
        $documentFields = [];

        foreach ($field->types as $type => $typedFields) {
            if (!isset($raw[$name][$type])) {
                continue;
            }

            $dataList = $field->multiple ? $raw[$name][$type] : [$raw[$name][$type]];

            foreach ($dataList as $data) {
                $documentData = \array_replace([$field->typeField => $type], $this->unmarshall($typedFields, $data));

                if ($field->multiple) {
                    /** @var string|int|null $originalIndex */
                    $originalIndex = $data['_originalIndex'] ?? null;
                    if ($originalIndex === null) {
                        throw new \RuntimeException('Expected "_originalIndex" field not found in document.');
                    }

                    $documentFields[$name][$originalIndex] = $documentData;

                    \ksort($documentFields[$name]);

                    continue;
                }

                $documentFields[$name] = $documentData;
            }
        }

        return $documentFields;
    }

    /**
     * @param array<string, mixed> $document
     *
     * @return array<string, mixed>
     */
    private function unmarshallObjectFields(array $raw, Field\ObjectField $field): array
    {
        if (!$field->multiple) {
            return $this->unmarshall($field->fields, $raw);
        }

        $documentFields = [];
        foreach ($raw as $data) {
            $documentFields[] = $this->unmarshall($field->fields, $data);
        }

        return $documentFields;
    }

    /**
     * @return string
     */
    private function unmarshallDateTimeField(?string $value, Field\DateTimeField $field): string
    {
        if ($field->multiple) {
            return \array_map(function($value) {
                if ($value !== null && $this->dateAsInteger) {
                    return date('c', $value);
                }

                return $value;
            }, $value);
        }

        if ($value !== null && $this->dateAsInteger) {
            return date('c', $value);
        }

        return $value;
    }
}
