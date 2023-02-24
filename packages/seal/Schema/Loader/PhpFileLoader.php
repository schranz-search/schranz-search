<?php

namespace Schranz\Search\SEAL\Schema\Loader;

use Schranz\Search\SEAL\Schema\Field;
use Schranz\Search\SEAL\Schema\Index;
use Schranz\Search\SEAL\Schema\Schema;

final class PhpFileLoader implements LoaderInterface
{
    /**
     * @param string[] $directories
     */
    public function __construct(
        private readonly array $directories,
    ) {
    }

    public function load(): Schema
    {
        /** @var Index[] $indexes */
        $indexes = [];

        foreach ($this->directories as $directory) {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($directory),
                \RecursiveIteratorIterator::LEAVES_ONLY,
            );

            foreach ($iterator as $file) {
                if ($file->getFileInfo()->getExtension() !== 'php') {
                    continue;
                }

                $index = require $file->getRealPath();

                if (!$index instanceof Index) {
                    throw new \RuntimeException(sprintf('File "%s" must return an instance of "%s".', $file->getRealPath(), Index::class));
                }

                if(isset($indexes[$index->name])) {
                    $index = new Index($index->name, $this->mergeFields($indexes[$index->name]->fields, $index->fields));
                }

                $indexes[$index->name] = $index;
            }
        }

        return new Schema($indexes);
    }

    /**
     * @param Field\AbstractField[] $fields
     * @param Field\AbstractField[] $newFields
     *
     * @return Field\AbstractField[]
     */
    private function mergeFields(array $fields, array $newFields): array
    {
        foreach ($newFields as $name => $newField) {
            if (isset($fields[$name])) {
                if ($newField::class !== $fields[$name]::class) {
                    throw new \RuntimeException(sprintf('Field "%s" must be of type "%s" but "%s" given.', $name, $fields[$name]::class, $newField::class));
                }

                $newField = $this->mergeField($fields[$name], $newField);
            }

            $fields[$newField->name] = $newField;
        }

        return $fields;
    }

    /**
     * @template T of Field\AbstractField
     *
     * @param T $field
     * @param T $newField
     *
     * @return T
     */
    private function mergeField(Field\AbstractField $field, Field\AbstractField $newField): Field\AbstractField
    {
        if ($newField instanceof Field\IdentifierField) {
            return $newField;
        }

        if ($newField instanceof Field\TextField) {
            return new Field\TextField(
                $newField->name,
                multiple: $newField->multiple,
                searchable: $newField->searchable,
                filterable: $newField->filterable,
                sortable: $newField->sortable,
                options: \array_replace_recursive($field->options, $newField->options),
            );
        }

        if ($newField instanceof Field\IntegerField) {
            return new Field\IntegerField(
                $newField->name,
                multiple: $newField->multiple,
                searchable: $newField->searchable,
                filterable: $newField->filterable,
                sortable: $newField->sortable,
                options: \array_replace_recursive($field->options, $newField->options),
            );
        }

        if ($newField instanceof Field\FloatField) {
            return new Field\FloatField(
                $newField->name,
                multiple: $newField->multiple,
                searchable: $newField->searchable,
                filterable: $newField->filterable,
                sortable: $newField->sortable,
                options: \array_replace_recursive($field->options, $newField->options),
            );
        }

        if ($newField instanceof Field\DateTimeField) {
            return new Field\DateTimeField(
                $newField->name,
                multiple: $newField->multiple,
                searchable: $newField->searchable,
                filterable: $newField->filterable,
                sortable: $newField->sortable,
                options: \array_replace_recursive($field->options, $newField->options),
            );
        }

        if ($newField instanceof Field\ObjectField) {
            return new Field\ObjectField(
                $newField->name,
                fields: $this->mergeFields($field->fields, $newField->fields),
                multiple: $newField->multiple,
                options: \array_replace_recursive($field->options, $newField->options),
            );
        }

        if ($newField instanceof Field\TypedField) {
            $types = $field->types;
            foreach ($newField->types as $name => $newTypedFields) {
                if (isset($types[$name])) {
                    $types[$name] = $this->mergeFields($types[$name], $newTypedFields);

                    continue;
                }

                $types[$name] = $newTypedFields;
            }

            return new Field\TypedField(
                $newField->name,
                typeField: $newField->typeField,
                types: $types,
                multiple: $newField->multiple,
                options: \array_replace_recursive($field->options, $newField->options),
            );
        }

        throw new \RuntimeException(sprintf(
            'Field "%s" must be of type "%s" but "%s" given.',
            $field->name,
            Field\AbstractField::class,
            get_class($field)
        ));
    }
}
