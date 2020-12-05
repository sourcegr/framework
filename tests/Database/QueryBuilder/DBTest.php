<?php

namespace Sourcegr\Tests\Database\QueryBuilder;


use Sourcegr\Framework\Database\DBConnectionManager;
use Sourcegr\Framework\Database\DummyGrammar;
use Sourcegr\Framework\Database\TextDumpGrammar;
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
        $cm = new DBConnectionManager();
        $cm->create('default', 'dummy', []);

        $db = new DB($cm->getConnection('default'));

        $this->assertEquals(DB::class, get_class($db), 'testCreatesDB');
    }

    public function testThrowsOnNoGrammar(): void
    {
        $this->expectException(ArgumentCountError::class);
        $cm = new DBConnectionManager();
        $cm->create('default', 'dummy', []);

        $db = new DB();
    }

    public function testThrowsOnUnfitGrammar(): void
    {
        $this->expectException(TypeError::class);
        $db = new DB(null);
    }

    public function testReturnsQB(): void
    {
        $cm = new DBConnectionManager();
        $cm->create('default', 'dummy', []);

        $db = new DB($cm->getConnection('default'));

        $qb = $db->Table('table');
        $this->assertEquals(QueryBuilder::class, get_class($qb), 'testReturnsQB');
    }

    public function testGetGrammar(): void
    {
        $cm = new DBConnectionManager();
        $cm->create('default', 'dummy', []);

        $db = new DB($cm->getConnection('default'));

        $actual = $db->getGrammar();

        $this->assertInstanceOf(DummyGrammar::class, $actual, 'testGetGrammar');
    }

    public function testSetGrammar(): void
    {
        $cm = new DBConnectionManager();
        $cm->create('default', 'dummy', []);
        $db = new DB($cm->getConnection('default'));

        $grammar1 = new TextDumpGrammar(new \PDO('sqlite::memory:'));

        $this->assertInstanceOf(DummyGrammar::class, $db->getGrammar(), 'testSetGrammar');

        $db->setGrammar($grammar1);
        $actual = $db->getGrammar();

        $this->assertInstanceOf(TextDumpGrammar::class, $actual, 'testSetGrammar');
    }
}