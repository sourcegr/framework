<?php

namespace Sourcegr\Tests\Database\QueryBuilder;


use Sourcegr\Framework\Database\DBConnectionManager;
use Sourcegr\Framework\Database\QueryBuilder\QueryBuilder;
use Sourcegr\Framework\Database\QueryBuilder\DB;
use Sourcegr\Stub\Grammar;
use PHPUnit\Framework\TestCase;


class QueryBuilderTest extends TestCase
{
    /*
     *
     */
    static $table = 'table';

    private function init()
    {
        $cm = new DBConnectionManager();
        $cm->create('default', 'dummy', []);
        $db = new DB($cm->getConnection('default'));

        return $db->Table(static::$table);
    }


    public function testGetTable()
    {
        $res = $this->init();

        $expected = static::$table;
        $actual = $res->getTable();
        $this->assertEquals($expected, $actual, 'testGetTable');
    }

    public function testSetTable()
    {
        $res = $this->init();

        $expected = static::$table;
        $actual = $res->getTable();
        $this->assertEquals($expected, $actual, 'testSetTable');
    }

    public function testColumns()
    {
        $all = [
            "" => ['', null],
            "*" => ['*', null],
            "id" => ['id', ['id']],
            "id,name" => ["id,name", ['id', 'name']],
            "[]" => [[], null],
            "['id']" => [['id'], ['id']],
            "['id', 'name']" => [['id', 'name'], ['id', 'name']],
        ];

        foreach ($all as $label => $def) {
            $res = $this->init();
            [$send, $expected] = $def;
            $res->columns($send);
            $actual = $res->getCols();
            $this->assertEquals($expected, $actual, 'testColumns: ' . $label);
        }

        // no params
        $res = $this->init();
        $res->columns();
        $expected = null;
        $actual = $res->getCols();
        $this->assertEquals($expected, $actual, 'testColumns with no params');

        // many params
        $res = $this->init();
        $res->columns('id', 'name');
        $expected = ['id', 'name'];
        $actual = $res->getCols();
        $this->assertEquals($expected, $actual, 'testColumns with many params');
    }




    /*
    public function testCreateSQLWhere() {
        $res = $this->init();
        $actual = $res->XXX->->createSQLWhere();

        $expected = XXX;
        $this->assertEquals($expected, $actual, 'testCreateSQLWhere');
    }

    public function testCreateSQLLimit() {
        $res = $this->init();
        $actual = $res->XXX->->createSQLWhere();

        $expected = XXX;
        $this->assertEquals($expected, $actual, 'testCreateSQLLimit');
    }

    public function testCreateSelect() {
        $res = $this->init();
        $actual = $res->XXX->->createSQLWhere();

        $expected = XXX;
        $this->assertEquals($expected, $actual, 'testCreateSelect');
    }

    public function testSelect() {
        $res = $this->init();
        $actual = $res->XXX->->createSQLWhere();

        $expected = XXX;
        $this->assertEquals($expected, $actual, 'testSelect');
    }

    public function testUpdate() {
        $res = $this->init();
        $actual = $res->XXX->->createSQLWhere();

        $expected = XXX;
        $this->assertEquals($expected, $actual, 'testUpdate');
    }

    public function testInsert() {
        $res = $this->init();
        $actual = $res->XXX->->createSQLWhere();

        $expected = XXX;
        $this->assertEquals($expected, $actual, 'testInsert');
    }

 */
}