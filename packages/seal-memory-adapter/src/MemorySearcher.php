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

namespace Schranz\Search\SEAL\Adapter\Memory;

use Schranz\Search\SEAL\Adapter\SearcherInterface;
use Schranz\Search\SEAL\Marshaller\Marshaller;
use Schranz\Search\SEAL\Schema\Field;
use Schranz\Search\SEAL\Schema\Index;
use Schranz\Search\SEAL\Search\Condition;
use Schranz\Search\SEAL\Search\Result;
use Schranz\Search\SEAL\Search\Search;

final class MemorySearcher implements SearcherInterface
{
    private readonly Marshaller $marshaller;

    public function __construct()
    {
        $this->marshaller = new Marshaller();
    }

    public function search(Search $search): Result
    {
        $documents = [];

        $searchTerms = [];

        /** @var Index $index */
        foreach ($search->indexes as $index) {
            foreach (MemoryStorage::getDocuments($index) as $identifier => $document) {
                $identifier = (string) $identifier;

                if ([] === $search->filters) {
                    $documents[] = $document;

                    continue;
                }

                foreach ($search->filters as $filter) {
                    if ($filter instanceof Condition\IdentifierCondition) {
                        if ($filter->identifier !== $identifier) {
                            continue 2;
                        }
                    } elseif ($filter instanceof Condition\SearchCondition) {
                        $searchableDocument = $this->getSearchableDocument($index->fields, $document);

                        $text = \json_encode($searchableDocument, \JSON_THROW_ON_ERROR);
                        $terms = \explode(' ', $filter->query);
                        $searchTerms = \array_unique([...$searchTerms, ...$terms]);

                        foreach ($terms as $term) {
                            if (!\str_contains($text, $term)) {
                                continue 3;
                            }
                        }
                    } elseif ($filter instanceof Condition\EqualCondition) {
                        if (\str_contains($filter->field, '.')) {
                            throw new \RuntimeException('Nested fields are not supported yet.');
                        }

                        $values = (array) ($document[$filter->field] ?? []);

                        if (!\in_array($filter->value, $values, true)) {
                            continue 2;
                        }
                    } elseif ($filter instanceof Condition\NotEqualCondition) {
                        if (\str_contains($filter->field, '.')) {
                            throw new \RuntimeException('Nested fields are not supported yet.');
                        }

                        $values = (array) ($document[$filter->field] ?? []);

                        if (\in_array($filter->value, $values, true)) {
                            continue 2;
                        }
                    } elseif ($filter instanceof Condition\GreaterThanCondition) {
                        if (\str_contains($filter->field, '.')) {
                            throw new \RuntimeException('Nested fields are not supported yet.');
                        }

                        $values = (array) ($document[$filter->field] ?? []);
                        $hasMatchingValue = false;
                        foreach ($values as $value) {
                            if ($value > $filter->value) {
                                $hasMatchingValue = true;
                            }
                        }

                        if (false === $hasMatchingValue) {
                            continue 2;
                        }
                    } elseif ($filter instanceof Condition\GreaterThanEqualCondition) {
                        if (\str_contains($filter->field, '.')) {
                            throw new \RuntimeException('Nested fields are not supported yet.');
                        }

                        $values = (array) ($document[$filter->field] ?? []);

                        $hasMatchingValue = false;
                        foreach ($values as $value) {
                            if ($value >= $filter->value) {
                                $hasMatchingValue = true;
                            }
                        }

                        if (false === $hasMatchingValue) {
                            continue 2;
                        }
                    } elseif ($filter instanceof Condition\LessThanCondition) {
                        if (\str_contains($filter->field, '.')) {
                            throw new \RuntimeException('Nested fields are not supported yet.');
                        }

                        $values = (array) ($document[$filter->field] ?? []);

                        $hasMatchingValue = false;
                        foreach ($values as $value) {
                            if ($value < $filter->value) {
                                $hasMatchingValue = true;
                            }
                        }

                        if (false === $hasMatchingValue) {
                            continue 2;
                        }
                    } elseif ($filter instanceof Condition\LessThanEqualCondition) {
                        if (\str_contains($filter->field, '.')) {
                            throw new \RuntimeException('Nested fields are not supported yet.');
                        }

                        $values = (array) ($document[$filter->field] ?? []);

                        $hasMatchingValue = false;
                        foreach ($values as $value) {
                            if ($value <= $filter->value) {
                                $hasMatchingValue = true;
                            }
                        }

                        if (false === $hasMatchingValue) {
                            continue 2;
                        }
                    } else {
                        throw new \LogicException($filter::class . ' filter not implemented.');
                    }
                }

                $documents[] = $this->marshaller->unmarshall($index->fields, $document);
            }
        }

        $sortBys = \array_reverse($search->sortBys);
        foreach ($sortBys as $field => $direction) {
            \usort($documents, function ($docA, $docB) use ($field, $direction) {
                if ('desc' === $direction) {
                    return $docB[$field] <=> $docA[$field];
                }

                return $docA[$field] <=> $docB[$field];
            });
        }

        $documents = \array_slice($documents, $search->offset, $search->limit);

        $generator = (function () use ($documents, $search, $searchTerms): \Generator {
            foreach ($documents as $document) {
                foreach ($search->highlightFields as $highlightField) {
                    $highlightFieldContent = \json_encode($document[$highlightField], \JSON_THROW_ON_ERROR);
                    foreach ($searchTerms as $searchTerm) {
                        $highlightFieldContent = \str_replace(
                            $searchTerm,
                            $search->highlightPreTag . $searchTerm . $search->highlightPostTag,
                            $highlightFieldContent,
                        );
                    }

                    $highlightFieldContent = \str_replace(
                        $search->highlightPostTag . $search->highlightPostTag,
                        '',
                        $highlightFieldContent,
                    );

                    $highlightFieldContent = \str_replace(
                        $search->highlightPostTag . ' ' . $search->highlightPostTag,
                        ' ',
                        $highlightFieldContent,
                    );

                    $document['_formatted'] ??= [];

                    \assert(
                        \is_array($document['_formatted']),
                        'Document with key "_formatted" expected to be array.',
                    );

                    $document['_formatted'][$highlightField] = \json_decode($highlightFieldContent, true, 512, \JSON_THROW_ON_ERROR);
                }

                yield $document;
            }
        });

        return new Result(
            $generator(),
            \count($documents),
        );
    }

    /**
     * @param Field\AbstractField[] $fields
     * @param array<string, mixed> $document
     *
     * @return array<string, mixed>
     */
    private function getSearchableDocument(array $fields, array $document): array
    {
        foreach ($fields as $field) {
            if (!isset($document[$field->name])) {
                continue;
            }

            if (!$field->searchable) {
                unset($document[$field->name]);

                continue;
            }

            match (true) {
                $field instanceof Field\ObjectField => $document[$field->name] = $this->getSearchableObjectFields($field, $document[$field->name]), // @phpstan-ignore-line
                $field instanceof Field\TypedField => $document[$field->name] = $this->getSearchableTypedFields($field, $document[$field->name]), // @phpstan-ignore-line
                default => null,
            };
        }

        return $document;
    }

    /**
     * @param array<string, mixed>|array<array<string, mixed>> $data
     *
     * @return array<string, mixed>|array<array<string, mixed>>
     */
    private function getSearchableObjectFields(Field\ObjectField $field, array $data)
    {
        if (!$field->multiple) {
            return $this->getSearchableDocument($field->fields, $data);
        }

        /** @var array<array<string, mixed>> $documents */
        $documents = [];

        /** @var array<string, mixed> $sub */
        foreach ($data as $sub) {
            $documents[] = $this->getSearchableDocument($field->fields, $sub);
        }

        return $documents;
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>
     */
    private function getSearchableTypedFields(Field\TypedField $field, array $data)
    {
        $documents = [];
        foreach ($data as $type => $sub) {
            if (!$field->multiple) {
                $sub = [$sub];
            }

            /** @var array<array<string, mixed>> $sub */
            $typeFields = $field->types[$type];
            foreach ($sub as $item) {
                $subDocument = $this->getSearchableDocument($typeFields, $item);

                if (!$field->multiple) {
                    return [$type => $subDocument];
                }

                $documents[$type][] = $subDocument;
            }
        }

        return $documents;
    }
}
