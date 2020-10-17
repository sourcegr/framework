<?php

namespace Sourcegr\Tests\Database\QueryBuilder;


use Sourcegr\Framework\Database\QueryBuilder\Grammar\TextDumpGrammar;
use Sourcegr\Framework\Database\QueryBuilder\QueryBuilder;
use Sourcegr\Framework\Database\QueryBuilder\DB;
use Sourcegr\Stub\Grammar;
use ArgumentCountError;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use TypeError;

class DBTest extends TestCase
{
    public function testCreatesDB(): void
    {
        $grammar = new TextDumpGrammar(new \PDO('sqlite::memory:'));
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
        $this->expectException(TypeError::class);
        $db = new DB(null);
    }

    public function testReturnsQB(): void
    {
        $grammar = new TextDumpGrammar(new \PDO('sqlite::memory:'));
        $db = new DB($grammar);
        $qb = $db->Table('table');
        $this->assertEquals(QueryBuilder::class, get_class($qb), 'testReturnsQB');
    }

    public function testGetGrammar(): void
    {
        $grammar = new TextDumpGrammar(new \PDO('sqlite::memory:'));
        $db = new DB($grammar);

        $actual = $db->getGrammar();

        $this->assertEquals($grammar, $actual, 'testGetGrammar');
    }

    public function testSetGrammar(): void
    {
        $grammar = new TextDumpGrammar(new \PDO('sqlite::memory:'));
        $grammar1 = new TextDumpGrammar(new \PDO('sqlite::memory:'));
        $db = new DB($grammar);

        $this->assertEquals($grammar, $db->getGrammar(), 'testSetGrammar');

        $db->setGrammar($grammar1);
        $actual = $db->getGrammar();

        $this->assertEquals($grammar1, $actual, 'testSetGrammar');
    }



}