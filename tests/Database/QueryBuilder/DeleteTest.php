<?php

namespace Sourcegr\Tests\Database\QueryBuilder;


use Sourcegr\Framework\Database\QueryBuilder\Grammar\TextDumpGrammar;
use Sourcegr\Framework\Database\QueryBuilder\QueryBuilder;
use Sourcegr\Framework\Database\QueryBuilder\DB;
use Sourcegr\Framework\Database\QueryBuilder\Raw;
use Sourcegr\Framework\Database\QueryBuilder\Exceptions\DeleteErrorException;
use Sourcegr\Stub\Grammar;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class DeleteTest extends TestCase
{
    static $table = 'table';

    private function init()
    {
        $grammar = new TextDumpGrammar(new \PDO('sqlite::memory:'));
        $qb = new DB($grammar);
        return $qb->Table(static::$table);
    }

    public function testDeleteAll()
    {
        $res = $this->init();
        [$actual, $params] = $res->delete();

        $expected = 'DELETE FROM ' . static::$table;
        $expectedParams = [];

        $this->assertEquals($expected, $actual, 'testDelete SQL');
        $this->assertEquals($expectedParams, $params, 'testDelete params');
    }
    public function testDeleteWhere()
    {
        $res = $this->init();
        [$actual, $params] = $res->where('id', 1)->delete();

        $expected = 'DELETE FROM ' . static::$table.' WHERE id = ?';
        $expectedParams = [1];

        $this->assertEquals($expected, $actual, 'testDelete SQL');
        $this->assertEquals($expectedParams, $params, 'testDelete params');
    }
}