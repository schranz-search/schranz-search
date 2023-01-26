<?php

namespace Schranz\Search\SEAL\Marshaller;

use Schranz\Search\SEAL\Schema\Field;

/**
 * @internal This class currently in discussion to be open for all adapters.
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
        $rawDocument = $this->marshaller->marshall($fields, $document);

        return $this->flatten($fields, $rawDocument);
    }

    /**
     * @param Field\AbstractField[] $fields
     * @param array<string, mixed> $raw
     *
     * @return array<string, mixed>
     */
    public function unmarshall(array $fields, array $raw): array
    {
        $rawDocument = $this->unflatten($fields, $raw);

        return $this->marshaller->unmarshall($fields, $rawDocument);
    }

    /**
     * @param Field\AbstractField[] $fields
     * @param array<string, mixed> $raw
     *
     * @return array<string, mixed>
     */
    private function flatten(array $fields, array $raw, bool $isParentMultiple = false)
    {
        foreach ($fields as $name => $field) {
            if (!array_key_exists($name, $raw)) {
                continue;
            }

            match (true) {
                $field instanceof Field\ObjectField => $raw = $this->flattenObject($name, $raw, $field, $isParentMultiple),
                $field instanceof Field\TypedField => $raw = $this->flattenTyped($name, $raw, $field, $isParentMultiple),
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
        foreach ($objects as $index => $object) {
            $isParentMultiple = $rootIsParentMultiple || $field->multiple;
            foreach ($this->flatten($field->fields, $object, $isParentMultiple) as $key => $value) {
                if ($isParentMultiple && isset($field->fields[$key]) && $field->fields[$key]->multiple) {
                    $newRawData[$name . '.' . $key . '._originalLength'][$index] = count($value);
                    $filler = array_fill(0, $index + 1, 0);
                    $newRawData[$name . '.' . $key . '._originalLength'] = array_replace($filler, $newRawData[$name . '.' . $key . '._originalLength']);

                    if (!isset($newRawData[$name . '.' . $key])) {
                        $newRawData[$name . '.' . $key] = [];
                    }

                    \array_push($newRawData[$name . '.' . $key], ...($value));

                    continue;
                }

                if ($isParentMultiple && !is_array($value)) {
                    $newRawData[$name . '.' . $key][$index] = $value;
                    $filler = array_fill(0, $index + 1, 0);
                    $newRawData[$name . '.' . $key] = array_replace($filler, $newRawData[$name . '.' . $key]);

                    continue;
                }

                if (str_ends_with($key, '._originalLength')) {
                    $newRawData[$name . '.' . $key] = $value;

                    continue;
                }

                if ($isParentMultiple) {
                    if (isset($newRawData[$name . '.' . $key . '._originalLength'])) {
                        $newRawData[$name . '.' . $key . '._originalLength'][$index] = count($value);
                        $filler = array_fill(0, $index + 1, 0);
                        $newRawData[$name . '.' . $key . '._originalLength'] = array_replace($filler, $newRawData[$name . '.' . $key . '._originalLength']);
                    }

                    $newRawData[$name . '.' . $key] = [...($newRawData[$name . '.' . $key] ?? []), ...$value];

                    continue;
                }

                $newRawData[$name . '.' . $key] = $value;
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
        $types = $raw[$name];

        $newRawData = [];
        foreach ($types as $type => $object) {
            $objects = $field->multiple ? $object : [$object];

            foreach ($objects as $index => $object) {
                $isParentMultiple = $rootIsParentMultiple || $field->multiple;
                foreach ($this->flatten($field->types[$type], $object, $isParentMultiple) as $key => $value) {
                    if (\is_array($value)) {
                        $newRawData[$name . '.' . $type . '.' . $key . '._originalLength'][$index] = count($value);
                        $filler = array_fill(0, $index + 1, 0);
                        $newRawData[$name . '.' . $type . '.' . $key . '._originalLength'] = array_replace($filler, $newRawData[$name . '.' . $type . '.' . $key . '._originalLength']);

                        if (!isset($newRawData[$name . '.' . $type . '.' . $key])) {
                            $newRawData[$name . '.' . $type . '.' . $key] = [];
                        }

                        \array_push($newRawData[$name . '.' . $type . '.' . $key], ...$value);

                        continue;
                    } elseif ($field->multiple) {
                        $newRawData[$name . '.' . $type . '.' . $key][$index] = $value;
                        $filler = array_fill(0, $index + 1, null);
                        $newRawData[$name . '.' . $type . '.' . $key] = array_replace($filler, $newRawData[$name . '.' . $type . '.' . $key]);

                        continue;
                    }

                    $newRawData[$name . '.' . $type . '.' . $key] = $value;
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

            $fieldTypes['_originalIndex'] = new Field\IntegerField(
                '_originalIndex',
                multiple: true,
                searchable: false,
            );

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
            foreach ($value as $subKey => $subValue) {
                if (str_ends_with($key, '._originalLength')) {
                    continue;
                }

                if (isset($object[$key . '._originalLength'])) {
                    $count = $object[$key . '._originalLength'];
                    if (isset($object[$key . '._originalLength._originalLength'])) {
                        $count = [];
                        for ($i = 0; $i < ($object[$key . '._originalLength._originalLength'][$subKey] ?? 0); ++$i) {
                            $count[] = array_shift($object[$key . '._originalLength']);
                        }
                    }

                    for ($i = 0; $i < ($count[$subKey] ?? 0); ++$i) {
                        $subRawData[$subKey][$key][] = array_shift($value);
                    }

                    continue;
                }

                $subRawData[$subKey][$key] = $subValue;
            }
        }

        $subRawData = array_values($subRawData);

        return $subRawData;
    }
}
