<?php

namespace Sourcegr\Tests\Database\QueryBuilder;


use Sourcegr\Framework\Database\QueryBuilder\QueryBuilder;
use Sourcegr\Framework\Database\QueryBuilder\DB;
use Sourcegr\Stub\Grammar;
use ArgumentCountError;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class DBTest extends TestCase
{
    public function testCreatesDB(): void
    {
        $grammar = new Grammar();
        $db = new DB($grammar);
        $this->assertEquals(DB::class, get_class($db), 'testCreatesDB');
    }

    public function testThrowsOnNoGrammar(): void
    {
        $this->expectException(ArgumentCountError::class);
        $db = new DB();
    }

    public function testThrowsOnUnfitGrammar(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $db = new DB(null);
    }

    public function testReturnsQB(): void
    {
        $grammar = new Grammar();
        $db = new DB($grammar);
        $qb = $db->Table('table');
        $this->assertEquals(QueryBuilder::class, get_class($qb), 'testReturnsQB');
    }

    public function testGetGrammar(): void
    {
        $grammar = new Grammar();
        $db = new DB($grammar);

        $actual = $db->getGrammar();

        $this->assertEquals($grammar, $actual, 'testGetGrammar');
    }

    public function testSetGrammar(): void
    {
        $grammar = new Grammar();
        $db = new DB('no grammar');
        $this->assertEquals('no grammar', $db->getGrammar(), 'testSetGrammar');

        $db->setGrammar($grammar);
        $actual = $db->getGrammar();

        $this->assertEquals($grammar, $actual, 'testSetGrammar');
    }



}