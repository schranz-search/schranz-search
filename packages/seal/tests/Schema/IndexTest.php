<?php

namespace Schranz\Search\SEAL\Tests\Schema;


use PHPUnit\Framework\TestCase;
use Schranz\Search\SEAL\Schema\Index;
use Schranz\Search\SEAL\Schema\Field;

class IndexTest extends TestCase
{
    public function testIndex(): void
    {
        $index = new Index('test', [
            'uuid' => new Field\IdentifierField('uuid'),
            'object' => new Field\ObjectField('object', [
                'name' => new Field\TextField('name'),
            ]),
        ]);

        $this->assertSame('uuid', $index->getIdentifierField()->name);
        $this->assertSame(['object.name'], $index->searchableFields);
    }

    public function testFalseRootFieldMapping(): void
    {
        $this->expectException(\AssertionError::class);
        $this->expectExceptionMessage('A field named "title" does not match key "name" in index "test", this is at current state required and may change in future.');

        (new Index('test', [
            'uuid' => new Field\IdentifierField('uuid'),
            'name' => new Field\TextField('title'),
        ]));
    }

    public function testFalseIdentifiertFieldMapping(): void
    {
        $this->expectException(\AssertionError::class);
        $this->expectExceptionMessage('A field named "uuid" does not match key "id" in index "test", this is at current state required and may change in future.');

        (new Index('test', [
            'id' => new Field\IdentifierField('uuid'),
        ]));
    }

    public function testFalseObjectFieldMapping(): void
    {
        $this->expectException(\AssertionError::class);
        $this->expectExceptionMessage('A field named "title" does not match key "name" in index "test", this is at current state required and may change in future.');

        (new Index('test', [
            'uuid' => new Field\IdentifierField('uuid'),
            'object' => new Field\ObjectField('object', [
                'name' => new Field\TextField('title'),
            ]),
        ]));
    }

    public function testFalseRootFieldCharacter(): void
    {
        $this->expectException(\AssertionError::class);
        $this->expectExceptionMessage('A field named "test.title" uses unsupported character in index "test", supported characters are "a-z", "A-Z", "0-9" and "_".');

        (new Index('test', [
            'uuid' => new Field\IdentifierField('uuid'),
            'test.title' => new Field\TextField('test.title'),
        ]));
    }

    public function testFalseObjectFieldCharacter(): void
    {
        $this->expectException(\AssertionError::class);
        $this->expectExceptionMessage('A field named "test.title" uses unsupported character in index "test", supported characters are "a-z", "A-Z", "0-9" and "_".');

        (new Index('test', [
            'uuid' => new Field\IdentifierField('uuid'),
            'object' => new Field\ObjectField('object', [
                'test.title' => new Field\TextField('test.title'),
            ]),
        ]));
    }
}
