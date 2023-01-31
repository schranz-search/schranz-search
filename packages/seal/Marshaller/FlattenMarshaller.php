<?php

namespace Schranz\Search\SEAL\Marshaller;

use Schranz\Search\SEAL\Schema\Field;

/**
 * @internal This class currently in discussion to be open for all adapters.
 *
 * The FlattenMarshaller will flatten all fields and save original document under a `_rawDocument` field.
 * The FlattenMarshaller should only be used when the Search Engine does not support nested objects and so
 *     the Marshaller should used in many cases instead.
 */
final class FlattenMarshaller
{
    private Marshaller $marshaller;

    public function __construct(
        bool $dateAsInteger = false,
    ) {
        $this->marshaller = new Marshaller($dateAsInteger);
    }

    /**
     * @param Field\AbstractField[] $fields
     * @param array<string, mixed> $object
     *
     * @return array<string, mixed>
     */
    public function marshall(array $fields, array $document): array
    {
        $flattenDocument = $this->flatten($fields, $document);
        $flattenDocument['_rawDocument'] = \json_encode($document, \JSON_THROW_ON_ERROR);

        return $flattenDocument;
    }

    /**
     * @param Field\AbstractField[] $fields
     * @param array<string, mixed> $raw
     *
     * @return array<string, mixed>
     */
    public function unmarshall(array $fields, array $raw): array
    {
        return \json_decode($raw['_rawDocument'], true, flags: \JSON_THROW_ON_ERROR);
    }

    /**
     * @param Field\AbstractField[] $fields
     * @param array<string, mixed> $raw
     *
     * @return array<string, mixed>
     */
    private function flatten(array $fields, array $raw, bool $rootIsParentMultiple = false)
    {
        foreach ($fields as $name => $field) {
            if (!array_key_exists($name, $raw)) {
                continue;
            }

            match (true) {
                $field instanceof Field\ObjectField => $raw = $this->flattenObject($name, $raw, $field, $rootIsParentMultiple),
                $field instanceof Field\TypedField => $raw = $this->flattenTyped($name, $raw, $field, $rootIsParentMultiple),
                default => null,
            };
        }

        return $raw;
    }


    /**
     * @param array<string, mixed> $raw
     *
     * @return array<string, mixed>
     */
    private function flattenObject(string $name, array $raw, Field\ObjectField $field, bool $rootIsParentMultiple)
    {
        $objects = $field->multiple ? $raw[$name] : [$raw[$name]];

        $newRawData = [];
        foreach ($objects as $object) {
            $isParentMultiple = $rootIsParentMultiple || $field->multiple;
            $flattenedObject = $this->flatten($field->fields, $object, $isParentMultiple);

            foreach ($flattenedObject as $key => $value) {
                $flattenKey = $name . '.' . $key;

                if (!$isParentMultiple) {
                    $newRawData[$flattenKey] = $value;

                    continue;
                }

                if (!isset($newRawData[$flattenKey])) {
                    $newRawData[$flattenKey] = [];
                }

                if (!is_array($value)) {
                    $newRawData[$flattenKey][] = $value;

                    continue;
                }

                foreach ($value as $valuePart) {
                    $newRawData[$flattenKey][] = $valuePart;
                }
            }
        }

        $keepOrderRaw = [];
        foreach ($raw as $key => $value) {
            if ($key === $name) {
                foreach ($newRawData as $key2 => $value2) {
                    $keepOrderRaw[$key2] = $value2;
                }

                continue;
            }

            $keepOrderRaw[$key] = $value;
        }

        return $keepOrderRaw;
    }

    /**
     * @param array<string, mixed> $raw
     *
     * @return array<string, mixed>
     */
    private function flattenTyped(string $name, array $raw, Field\TypedField $field, bool $rootIsParentMultiple)
    {
        $objects = $field->multiple ? $raw[$name] : [$raw[$name]];

        $newRawData = [];
        foreach ($objects as $object) {
            $type = $object[$field->typeField];
            unset($object[$field->typeField]);

            $isParentMultiple = $rootIsParentMultiple || $field->multiple;

            $flattenedObject = $this->flatten($field->types[$type], $object, $isParentMultiple);
            foreach ($flattenedObject as $key => $value) {
                $flattenKey = $name . '.' . $type . '.' . $key;

                if (!$isParentMultiple) {
                    $newRawData[$flattenKey] = $value;

                    continue;
                }

                if (!isset($newRawData[$flattenKey])) {
                    $newRawData[$flattenKey] = [];
                }

                if (!is_array($value)) {
                    $newRawData[$flattenKey][] = $value;

                    continue;
                }

                foreach ($value as $valuePart) {
                    $newRawData[$flattenKey][] = $valuePart;
                }
            }
        }

        $keepOrderRaw = [];
        foreach ($raw as $key => $value) {
            if ($key === $name) {
                foreach ($newRawData as $key2 => $value2) {
                    $keepOrderRaw[$key2] = $value2;
                }

                continue;
            }

            $keepOrderRaw[$key] = $value;
        }

        return $keepOrderRaw;
    }

    private function unflatten(array $fields, array $raw, bool $isParentMultiple = false)
    {
        foreach ($fields as $name => $field) {
            match (true) {
                $field instanceof Field\ObjectField => $raw = $this->unflattenObject($name, $raw, $field, $isParentMultiple),
                $field instanceof Field\TypedField => $raw = $this->unflattenTyped($name, $raw, $field, $isParentMultiple),
                default => null,
            };
        }

        return $raw;
    }


    /**
     * @param array<string, mixed> $raw
     *
     * @return array<string, mixed>
     */
    private function unflattenObject(string $name, array $raw, Field\ObjectField $field, bool $rootIsParentMultiple)
    {
        $rawFields = [];
        $firstKey = null;
        foreach ($raw as $key => $value) {
            if (str_starts_with($key, $name . '.')) {
                [,  $subName] = \explode('.', $key, 2);

                $rawFields[$subName] = $value;
                if ($firstKey === null) {
                    $firstKey = $key;

                    continue;
                }

                unset($raw[$key]);
            }
        }

        $newRawData = [];
        if (!$field->multiple) {
            $newRawData = $this->unflatten($field->fields, $rawFields, $rootIsParentMultiple);
        } else {
            foreach ($this->unflattenValue($rawFields) as $key => $value) {
                $newRawData[$key] = $this->unflatten($field->fields, $value, true);
            }
        }

        $keepOrderRaw = [];
        foreach ($raw as $key => $value) {
            if ($key === $firstKey) {
                $keepOrderRaw[$name] = $newRawData;

                continue;
            }

            $keepOrderRaw[$key] = $value;
        }

        return $keepOrderRaw;
    }

    /**
     * @param array<string, mixed> $raw
     *
     * @return array<string, mixed>
     */
    private function unflattenTyped(string $name, array $raw, Field\TypedField $field, bool $rootIsParentMultiple)
    {
        $rawFields = [];
        $firstKey = null;
        foreach ($raw as $key => $value) {
            if (str_starts_with($key, $name . '.')) {
                [, $type, $subName] = \explode('.', $key, 3);

                $rawFields[$type][$subName] = $value;
                if ($firstKey === null) {
                    $firstKey = $key;

                    continue;
                }

                unset($raw[$key]);
            }
        }

        $newRawData = [];
        foreach ($rawFields as $type => $object) {
            $fieldTypes = $field->types[$type];

            if (!$field->multiple) {
                $newRawData[$type] = $this->unflatten($fieldTypes, $object, $rootIsParentMultiple);

                continue;
            }

            foreach ($this->unflattenValue($object) as $key => $value) {
                $newRawData[$type][$key] = $this->unflatten($fieldTypes, $value, true);
            }
        }

        $keepOrderRaw = [];
        foreach ($raw as $key => $value) {
            if ($key === $firstKey) {
                $keepOrderRaw[$name] = $newRawData;

                continue;
            }

            $keepOrderRaw[$key] = $value;
        }

        return $keepOrderRaw;
    }

    /**
     * @param array $object
     *
     * @return array<string, mixed>
     */
    private function unflattenValue(array $object): array
    {
        $subRawData = [];
        foreach ($object as $key => $value) {
            if (str_ends_with($key, '._originalLength')) {
                continue;
            }

            if (isset($object[$key . '._originalLength'])) {
                $lengths = [];
                $innerOriginalLength = $key . '._originalLength';

                static $c = 0;
                ++$c;

                while (isset($object[$innerOriginalLength . '._originalLength'])) {
                    array_unshift($lengths, $innerOriginalLength);
                    $innerOriginalLength .= '._originalLength';
                }

                foreach ($object[$innerOriginalLength] as $subKey => $subValue) {
                    if ($subValue === 0) {
                        continue;
                    }

                    foreach ($lengths as $length) {
                        $counts = array_splice($object[$length], 0, $subValue);
                        $subRawData[$subKey][$length] = $counts;

                        $subValue = array_reduce($counts, function($carry, $item) {
                            $carry += $item;
                            return $carry;
                        }, 0);
                    }

                    $subRawData[$subKey][$key] = array_splice($object[$key], 0, $subValue);
                }

                continue;
            }

            if (!is_array($value)) {
                continue;
            }

            foreach ($value as $subKey => $subValue) {
                $subRawData[$subKey][$key] = $subValue;
            }
        }

        $subRawData = array_values($subRawData);

        return $subRawData;
    }
}
