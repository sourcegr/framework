<?php

namespace Sourcegr\Tests\Database\QueryBuilder;


use Sourcegr\Framework\Database\DBConnectionManager;
use Sourcegr\Framework\Database\QueryBuilder\DB;
use Sourcegr\Framework\Database\QueryBuilder\Raw;
use Sourcegr\Stub\Grammar;
use PHPUnit\Framework\TestCase;

class SelectTest extends TestCase
{

    static $table = 'table';

    private function init()
    {
        $cm = new DBConnectionManager();
        $cm->create('default', 'dummy', []);
        $db = new DB($cm->getConnection('default'));

        return $db->Table(static::$table);
    }


    public function testSelectParameters()
    {
        $res = $this->init();
        [$actual, $params] = $res->select('id');

        $expected = 'SELECT id FROM ' . static::$table . '';
        $this->assertEquals($expected, $actual, 'testSelectParameters');
    }

    public function testSelectWithRAW()
    {
        $res = $this->init();
        [$actual, $params] = $res->where('date_at', new Raw('NOW()'))->select();

        $expected = 'SELECT * FROM ' . static::$table . ' WHERE date_at = NOW()';
        $this->assertEquals($expected, $actual, 'testSelectWithRAW');
    }

    public function testJoin()
    {
        $res = $this->init();
        [$actual, $params] = $res->join('join_table', 'join_table.id=table.id')->select();

        $expected = 'SELECT * FROM ' . static::$table . ' INNER JOIN join_table ON join_table.id=table.id';
        $this->assertEquals($expected, $actual, 'testJoin');
    }

    public function testLeftJoin()
    {
        $res = $this->init();
        [$actual, $params] = $res->leftJoin('join_table', 'join_table.id=table.id')->select();

        $expected = 'SELECT * FROM ' . static::$table . ' LEFT JOIN join_table ON join_table.id=table.id';
        $this->assertEquals($expected, $actual, 'testLeftJoin');
    }

    public function testRightJoin()
    {
        $res = $this->init();
        [$actual, $params] = $res->rightJoin('join_table', 'join_table.id=table.id')->select();

        $expected = 'SELECT * FROM ' . static::$table . ' RIGHT JOIN join_table ON join_table.id=table.id';
        $this->assertEquals($expected, $actual, 'testRightJoin');
    }

    public function testLimitWithNoOffset()
    {
        $res = $this->init();
        [$actual, $params] = $res->limit(10)->select();

        $expected = 'SELECT * FROM ' . static::$table . ' LIMIT 10';
        $this->assertEquals($expected, $actual, 'testLimitWithNoOffset');
    }

    public function testOffsetLimit()
    {
        $res = $this->init();
        [$actual, $params] = $res->limit(10)->offset(20)->select();

        $expected = 'SELECT * FROM ' . static::$table . ' LIMIT 10 OFFSET 20';
        $this->assertEquals($expected, $actual, 'testOffsetLimit');
    }

    public function testOffsetWithNoLimit()
    {
        $res = $this->init();
        [$actual, $params] = $res->offset(20)->select();

        $expected = 'SELECT * FROM ' . static::$table . '';
        $this->assertEquals($expected, $actual, 'testOffsetWithNoLimit');
    }


    public function testOrderBy()
    {
        $res = $this->init();
        [$actual, $params] = $res->orderBy('id')->select();

        $expected = 'SELECT * FROM ' . static::$table . ' ORDER BY id';
        $this->assertEquals($expected, $actual, 'testOrderBy');
    }

    public function testOrderByNull()
    {
        $res = $this->init();
        [$actual, $params] = $res->orderBy(null)->select();

        $expected = 'SELECT * FROM ' . static::$table . '';
        $this->assertEquals($expected, $actual, 'testOrderByNull');
    }

    public function testOrderByEmptyString()
    {
        $res = $this->init();
        [$actual, $params] = $res->orderBy('')->select();

        $expected = 'SELECT * FROM ' . static::$table . '';
        $this->assertEquals($expected, $actual, 'testOrderByEmptyString');
    }


    public function testGroupBy()
    {
        $res = $this->init();
        [$actual, $params] = $res->groupBy('id')->select();

        $expected = 'SELECT * FROM ' . static::$table . ' GROUP BY id';
        $this->assertEquals($expected, $actual, 'testGroupBy');
    }

    public function testGroupByNull()
    {
        $res = $this->init();
        [$actual, $params] = $res->groupBy(null)->select();

        $expected = 'SELECT * FROM ' . static::$table . '';
        $this->assertEquals($expected, $actual, 'testGroupByNull');
    }

    public function testGroupByEmptyString()
    {
        $res = $this->init();
        [$actual, $params] = $res->groupBy('')->select();

        $expected = 'SELECT * FROM ' . static::$table . '';
        $this->assertEquals($expected, $actual, 'testGroupByEmptyString');
    }

    // new tests

    public function testWhereStrings()
    {
        $allStrings = [
            [['id'], 'SELECT * FROM '.static::$table.' WHERE id IS NOT NULL'],
            [['id', 3], 'SELECT * FROM '.static::$table.' WHERE id = ?'],
            [['id', '>', 3], 'SELECT * FROM '.static::$table.' WHERE id > ?'],
        ];

        foreach ($allStrings as $pair) {
            $send = $pair[0];
            $expected = $pair[1];
            [$actual, $params] = $this->init()->where(...$send)->select();

            $this->assertEquals($expected, $actual, 'testWhereStrings');
        }
    }

    public function testWhereAssocArray()
    {
        $expected = 'SELECT * FROM '.static::$table.' WHERE id = ? AND name = ?';
        [$actual, $params] = $this->init()->where(['id' => 1, 'name' => 'my name'])->select();
        $this->assertEquals($expected, $actual, 'testWhereAssocArray');
    }

    public function testWhereNumericArray()
    {
        $expected = 'SELECT * FROM '.static::$table.' WHERE id IS NOT NULL AND name IS NOT NULL';
        [$actual, $params] = $this->init()->where(['id', 'name'])->select();
        $this->assertEquals($expected, $actual, 'testWhereNumericArray');
    }

    public function testWhereMixedArray()
    {
        $expected = 'SELECT * FROM '.static::$table.' WHERE id IS NOT NULL AND name = ?';
        [$actual, $params] = $this->init()->where(['id', 'name' => 'my name'])->select();
        $this->assertEquals($expected, $actual, 'testWhereNumericArray');
    }


    public function testOrWhere()
    {
        $res = $this->init();
        [$actual, $params] = $res->where('id', 1)->orWhere('id', 2)->select();

        $expected = 'SELECT * FROM '.static::$table.' WHERE id = ? OR id = ?';
        $this->assertEquals($expected, $actual, 'testOrWhere');
    }


    public function testWhereIn()
    {
        $res = $this->init();
        [$actual, $params] = $res->whereIn('id', [1, 2, 3, 4])->select();

        $expected = 'SELECT * FROM '.static::$table.' WHERE id IN (?,?,?,?)';
        $this->assertEquals($expected, $actual, 'testWhereIn');
    }

    public function testWhereInWithString()
    {
        $res = $this->init();
        [$actual, $params] = $res->whereIn('id', '1, 2, 3, 4')->select();

        $expected = 'SELECT * FROM '.static::$table.' WHERE id IN (?,?,?,?)';
        $this->assertEquals($expected, $actual, 'testWhereInWithString');
    }

    public function testWhereNotIn()
    {
        $res = $this->init();
        [$actual, $params] = $res->whereNotIn('id', [1, 2, 3, 4])->select();
        $expected = 'SELECT * FROM '.static::$table.' WHERE id NOT IN (?,?,?,?)';
        $this->assertEquals($expected, $actual, 'testWhereNotIn');
    }


    public function testOrWhereIn()
    {
        $res = $this->init();
        [$actual, $params] = $res->where('id', 1)->orWhereIn('id', [1, 2, 3, 4])->select();

        $expected = 'SELECT * FROM '.static::$table.' WHERE id = ? OR id IN (?,?,?,?)';
        $this->assertEquals($expected, $actual, 'testOrWhereIn');
    }

    public function testWhereLike()
    {
        $res = $this->init();
        [$actual, $params] = $res->whereLike('name', 'papas')->select();

        $expected = 'SELECT * FROM '.static::$table.' WHERE name LIKE ?';
        $this->assertEquals($expected, $actual, 'testWhereLike');
    }

    public function testOrWhereLike()
    {
        $res = $this->init();
        [$actual, $params] = $res->where('id', 1)->orWhereLike('name', 'papas')->select();

        $expected = 'SELECT * FROM '.static::$table.' WHERE id = ? OR name LIKE ?';
        $this->assertEquals($expected, $actual, 'testOrWhereLike');
    }

    public function testWhereNotLike()
    {
        $res = $this->init();
        [$actual, $params] = $res->where('id', 1)->whereNotLike('name', 'papas')->select();

        $expected = 'SELECT * FROM '.static::$table.' WHERE id = ? AND name NOT LIKE ?';
        $this->assertEquals($expected, $actual, 'testWhereNotLike');
    }

    public function testOrWhereNotLike()
    {
        $res = $this->init();
        [$actual, $params] = $res->where('id', 1)->orWhereNotLike('name', 'papas')->select();

        $expected = 'SELECT * FROM '.static::$table.' WHERE id = ? OR name NOT LIKE ?';
        $this->assertEquals($expected, $actual, 'testOrWhereNotLike');
    }

    public function testOrWhereNotIn()
    {
        $res = $this->init();
        [$actual, $params] = $res->where('id', 1)->orWhereNotIn('id', [1, 2, 3])->select();

        $expected = 'SELECT * FROM '.static::$table.' WHERE id = ? OR id NOT IN (?,?,?)';
        $this->assertEquals($expected, $actual, 'testOrWhereNotIn');
    }


    public function testWhereNull()
    {
        $res = $this->init();
        [$actual, $params] = $res->whereNull('id')->select();

        $expected = 'SELECT * FROM '.static::$table.' WHERE id IS NULL';
        $this->assertEquals($expected, $actual, 'testWhereNull');
    }

    public function testOrWhereNull()
    {
        $res = $this->init();
        [$actual, $params] = $res->whereNull('id')->orWhereNull('parent_id')->select();

        $expected = 'SELECT * FROM '.static::$table.' WHERE id IS NULL OR parent_id IS NULL';
        $this->assertEquals($expected, $actual, 'testOrWhereNull');
    }

    public function testWhereNotNull()
    {
        $res = $this->init();
        [$actual, $params] = $res->whereNotNull('id')->select();

        $expected = 'SELECT * FROM '.static::$table.' WHERE id IS NOT NULL';
        $this->assertEquals($expected, $actual, 'testWhereNotNull');
    }

    public function testOrWhereNotNull()
    {
        $res = $this->init();
        [$actual, $params] = $res->whereNull('id')->orWhereNotNull('parent_id')->select();

        $expected = 'SELECT * FROM '.static::$table.' WHERE id IS NULL OR parent_id IS NOT NULL';
        $this->assertEquals($expected, $actual, 'testOrWhereNotNull');
    }

    public function testNestedQuery()
    {
        $res = $this->init();
        [$actual, $params] = $res->whereNull('id')->orWhere(
            function ($q) {
                $q->where('name', 'my name')->where('email', 'my@email.tld');
            }
        )->select();

        $expected = 'SELECT * FROM '.static::$table.' WHERE id IS NULL OR (name = ? AND email = ?)';
        $this->assertEquals($expected, $actual, 'testOrWhereNotNull');
    }

    public function testSubQuery()
    {
        $res = $this->init();

        [$actual, $params] = $res->whereNotNull('id')->orWhereIn(
            'id',
            $this->init()->columns('id')
        )->select();

        $expected = 'SELECT * FROM '.static::$table.' WHERE id IS NOT NULL OR id IN (SELECT id FROM ' . static::$table . ')';
        $this->assertEquals($expected, $actual, 'testOrWhereNotNull');
    }
}