<?php

namespace Schranz\Search\SEAL\Tests\Marshaller;

use PHPUnit\Framework\TestCase;
use Schranz\Search\SEAL\Marshaller\FlattenMarshaller;
use Schranz\Search\SEAL\Testing\TestingHelper;

class FlattenMarshallerTest extends TestCase
{
    public function testMarshallFlatten(): void
    {
        $marshaller = new FlattenMarshaller();
        $complexIndex = TestingHelper::createSchema()->indexes[TestingHelper::INDEX_COMPLEX];
        $documents = TestingHelper::createComplexFixtures();

        $rawDocument = $marshaller->marshall($complexIndex->fields, $documents[0]);

        $this->assertSame($this->getRawDocument(), $rawDocument);
    }

    public function testUnmarshall(): void
    {
        $marshaller = new FlattenMarshaller();
        $complexIndex = TestingHelper::createSchema()->indexes[TestingHelper::INDEX_COMPLEX];

        $document = $marshaller->unmarshall($complexIndex->fields, $this->getRawDocument());

        $documents = TestingHelper::createComplexFixtures();
        $this->assertSame($documents[0], $document);
    }

    /**
     * @return array<string, mixed>
     */
    private function getRawDocument(bool $dateAsInteger = false): array
    {
        return [
            'uuid' => '23b30f01-d8fd-4dca-b36a-4710e360a965',
            'title' => 'New Blog',
            'header.image.media' => 1,
            'article' => '<article><h2>New Subtitle</h2><p>A html field with some content</p></article>',
            'blocks.text._originalIndex' => [0, 1, 3],
            'blocks.text.title' => ['Titel', 'Titel 2', 'Titel 4'],
            'blocks.text.description' => ['<p>Description</p>', null, '<p>Description 4</p>'],
            'blocks.text.media.length' => [2, 0, 2],
            'blocks.text.media' => [3, 4, 3, 4],
            'blocks.embed._originalIndex' => [2],
            'blocks.embed.title' => ['Video'],
            'blocks.embed.media' => ['https://www.youtube.com/watch?v=iYM2zFP3Zn0'],
            'footer.title' => 'New Footer',
            'created' => $dateAsInteger ? 1643022000 : '2022-01-24T12:00:00+01:00',
            'commentsCount' => 2,
            'rating' => 3.5,
            'comments.email' => ['admin.nonesearchablefield@localhost', 'example.nonesearchablefield@localhost'],
            'comments.text' => ['Awesome blog!', 'Like this blog!'],
            'tags' => ['Tech', 'UI'],
            'categoryIds' => [1, 2],
        ];
    }
}
