<?php

namespace Schranz\Search\SEAL\Tests\Marshaller;

use PHPUnit\Framework\TestCase;
use Schranz\Search\SEAL\Marshaller\Marshaller;
use Schranz\Search\SEAL\Testing\TestingHelper;

class MarshallerTest extends TestCase
{
    private Marshaller $marshaller;

    public function setUp(): void
    {
        $this->marshaller = new Marshaller();
    }

    public function testMarshall(): void
    {
        $complexIndex = TestingHelper::createSchema()->indexes[TestingHelper::INDEX_COMPLEX];
        $documents = TestingHelper::createComplexFixtures();

        $rawDocument = $this->marshaller->marshall($complexIndex->fields, $documents[0]);

        $this->assertSame($this->getRawDocument(), $rawDocument);
    }

    public function testUnmarshall(): void
    {
        $complexIndex = TestingHelper::createSchema()->indexes[TestingHelper::INDEX_COMPLEX];

        $document = $this->marshaller->unmarshall($complexIndex->fields, $this->getRawDocument());

        $documents = TestingHelper::createComplexFixtures();
        $this->assertSame($documents[0], $document);
    }

    /**
     * @return array<string, mixed>
     */
    private function getRawDocument(): array
    {
        return [
            'uuid' => '23b30f01-d8fd-4dca-b36a-4710e360a965',
            'title' => 'New Blog',
            'header' => [
                'image' => [
                    'media' => 1,
                ],
            ],
            'article' => '<article><h2>New Subtitle</h2><p>A html field with some content</p></article>',
            'blocks' => [
                'text' => [
                    [
                        '_originalIndex' => 0,
                        'title' => 'Titel',
                        'description' => '<p>Description</p>',
                        'media' => [3, 4],
                    ],
                    [
                        '_originalIndex' => 1,
                        'title' => 'Titel 2',
                        'description' => '<p>Description 2</p>',
                    ],
                ],
                'embed' => [
                    [
                        '_originalIndex' => 2,
                        'title' => 'Video',
                        'media' => 'https://www.youtube.com/watch?v=iYM2zFP3Zn0',
                    ],
                ],
            ],
            'footer' => [
                'title' => 'New Footer',
            ],
            'created' => '2022-01-24T12:00:00+01:00',
            'commentsCount' => 2,
            'rating' => 3.5,
            'comments' => [
                [
                    'email' => 'admin.nonesearchablefield@localhost',
                    'text' => 'Awesome blog!',
                ],
                [
                    'email' => 'example.nonesearchablefield@localhost',
                    'text' => 'Like this blog!',
                ],
            ],
            'tags' => ['Tech', 'UI'],
            'categoryIds' => [1, 2],
        ];
    }
}
