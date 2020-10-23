<?php

namespace Sourcegr\Tests\Database\QueryBuilder;


use Sourcegr\Framework\Database\DBConnectionManager;
use Sourcegr\Framework\Database\QueryBuilder\DB;
use Sourcegr\Framework\Database\QueryBuilder\Raw;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class InsertTest extends TestCase
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

    public function testInsertArray()
    {
        $res = $this->init();
        [$actual, $params] = $res->insert(['name' => 'new name']);

        $expected = 'INSERT INTO ' . static::$table . ' (name) VALUES (?)';
        $expectedParams = ['new name'];

        $this->assertEquals($expected, $actual, 'testInsert SQL');
        $this->assertEquals($expectedParams, $params, 'testInsert params');
    }

    public function testInsertRaw()
    {
        $res = $this->init();
        [$actual, $params] = $res->insert(['date_at' => new Raw('NOW()')]);

        $expected = 'INSERT INTO ' . static::$table . ' (date_at) VALUES (NOW())';
        $expectedParams = [];

        $this->assertEquals($expected, $actual, 'testInsert SQL');
        $this->assertEquals($expectedParams, $params, 'testInsert params');
    }

    public function testFailsOnNoInput()
    {
        $res = $this->init();
        $this->expectException(InvalidArgumentException::class);
        $res->insert();
    }

    public function testFailsOnEmptyArray()
    {
        $res = $this->init();
        $this->expectException(InvalidArgumentException::class);
        $res->insert([]);
    }

    public function testFailsOnNumericKeys()
    {
        $res = $this->init();
        $this->expectException(InvalidArgumentException::class);
        $res->insert([1 => 'ERROR']);
    }

    public function testFailsOnMixedKeys()
    {
        $res = $this->init();
        $this->expectException(InvalidArgumentException::class);
        $res->insert(['name' => 'valid', '1' => 'ERROR']);
    }

    public function testFailsOnNoArray()
    {
        $res = $this->init();
        $this->expectException(InvalidArgumentException::class);
        $res->insert('a');
    }


//    public function testInsertWith2Parameters()
//    {
//        $res = $this->init();
//        [$actual, $params] = $res->update('name', 'new name');
//
//        $expected = 'UPDATE table SET name = ?';
//        $expectedParams = ['new name'];
//
//        $this->assertEquals($expected, $actual, 'testInsertWith2Parameters SQL');
//        $this->assertEquals($expectedParams, $params, 'testInsertWith2Parameters params');
//    }
//
//    public function testInsertRaw()
//    {
//        $res = $this->init();
//        [$actual, $params] = $res->update(['name'=> new Raw('NOW()')]);
//
//        $expected = 'UPDATE table SET name=NOW()';
//        $expectedParams = [];
//
//        $this->assertEquals($expected, $actual, 'testInsertRaw SQL');
//        $this->assertEquals($expectedParams, $params, 'testInsertRaw params');
//    }
//
//    public function testInsertWithWhere()
//    {
//        $res = $this->init();
//        [$actual, $params] = $res->where('id')->update(['name'=> 'new name']);
//
//        $expected = 'UPDATE table SET name = ? WHERE id IS NOT NULL';
//        $expectedParams = ['new name'];
//
//        $this->assertEquals($expected, $actual, 'testInsertWithWhere SQL');
//        $this->assertEquals($expectedParams, $params, 'testInsertWithWhere params');
//    }
//
//    public function testFailsOnMoreThan2Parameters() {
//        $res = $this->init();
//        $this->expectException(InvalidArgumentException::class);
//        $res->update('id', 'name', 'ILLEGAL');
//    }
//

//
//    public function testFailsOnNoArray()
//    {
//        $res = $this->init();
//        $this->expectException(InvalidArgumentException::class);
//        $res->update('a');
//    }
//
//    public function testReturnsZeroOnEmptyArray()
//    {
//        $res = $this->init();
//        $expected = 0;
//        $actual = $res->update([]);
//        $this->assertEquals($expected, $actual, 'testInsert');
//    }
//
//    public function testFailsOnNonAssocArray()
//    {
//        $res = $this->init();
//        $this->expectException(InvalidArgumentException::class);
//        $res->update(['id', 'name']);
//    }
//
//    public function testFailsOnLimit()
//    {
//        $res = $this->init();
//        $this->expectException(InsertErrorException::class);
//        $res->limit(4)->update(['name'=> 'new name']);
//    }
}