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
        return $this->marshaller->unmarshall($fields, $raw);
    }

    /**
     * @param Field\AbstractField[] $fields
     * @param array<string, mixed> $raw
     *
     * @return array<string, mixed>
     */
    private function flatten(array $fields, array $raw)
    {
        foreach ($fields as $name => $field) {
            if (!array_key_exists($name, $raw)) {
                continue;
            }

            match (true) {
                $field instanceof Field\ObjectField => $raw = $this->flattenObject($name, $raw, $field),
                $field instanceof Field\TypedField => $raw = $this->flattenTyped($name, $raw, $field),
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
    private function flattenObject(string $name, array $raw, Field\ObjectField $field)
    {
        $objects = $field->multiple ? $raw[$name] : [$raw[$name]];

        $newRawData = [];
        foreach ($objects as $index => $object) {
            foreach ($this->flatten($field->fields, $object) as $key => $value) {
                if (is_array($value)) {
                    $newRawData[$name . '.' . $key . '.length'][$index] = count($value);
                    $filler = array_fill(0, $index + 1, 0);
                    $newRawData[$name . '.' . $key . '.length'] = array_replace($filler, $newRawData[$name . '.' . $key . '.length']);

                    if (!isset($newRawData[$name . '.' . $key])) {
                        $newRawData[$name . '.' . $key] = [];
                    }

                    \array_push($newRawData[$name . '.' . $key], ...($value));

                    $newRawData[$name . '.' . $key] = array_merge($newRawData[$name . '.' . $key] ?? [], $value);

                    continue;
                } elseif ($field->multiple) {
                    $newRawData[$name . '.' . $key][] = $value;

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
    private function flattenTyped(string $name, array $raw, Field\TypedField $field)
    {
        $types = $raw[$name];

        $newRawData = [];
        foreach ($types as $type => $object) {
            $objects = $field->multiple ? $object : [$object];

            foreach ($objects as $index => $object) {
                foreach ($this->flatten($field->types[$type], $object) as $key => $value) {
                    if (\is_array($value)) {
                        $newRawData[$name . '.' . $type . '.' . $key . '.length'][$index] = count($value);
                        $filler = array_fill(0, $index + 1, 0);
                        $newRawData[$name . '.' . $type . '.' . $key . '.length'] = array_replace($filler, $newRawData[$name . '.' . $type . '.' . $key . '.length']);

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
}
