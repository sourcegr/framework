<?php

namespace Sourcegr\Tests\Database\QueryBuilder;


use Sourcegr\Framework\Database\QueryBuilder\Params;
use PHPUnit\Framework\TestCase;

class ParamsTest extends TestCase
{
    public function testStraightWhere(): void
    {
        $expected = [['id', '=', 1, 'AND']];
        $params = new Params(null);
        $params->where('id', '=', '1');
        $actual = $params->data['data'];

        $this->assertEquals($expected, $actual, 'testStraightWhere');
    }

    public function testManyWheres(): void
    {
        $expected = [['id', '=', 1, 'AND'], ['name', '=', 'papas', 'AND'],];
        $params = new Params(null);
        $params->where('id', '=', '1')->where('name', '=', 'papas');
        $actual = $params->data['data'];

        $this->assertEquals($expected, $actual, 'testManyWheres');
    }

    public function testNoEqualSighWhere(): void
    {
        $expected = [['id', '=', 1, 'AND']];
        $params = new Params(null);
        $params->where('id', '1');
        $actual = $params->data['data'];

        $this->assertEquals($expected, $actual, 'testStraightWhere');
    }

    public function testNoEqualSighNoValueWhere(): void
    {
        $expected = [['id', null, 'IS NOT NULL', 'AND']];
        $params = new Params(null);
        $params->where('id');
        $actual = $params->data['data'];
        $this->assertEquals($expected, $actual, 'testNoEqualSighNoValueWhere');
    }

    public function testWhereInArray(): void
    {
        $expected = [['id', 'IN', [1,2,3], 'AND']];
        $params = new Params(null);
        $params->where('id', 'IN', [1,2,3]);
        $actual = $params->data['data'];

        $this->assertEquals($expected, $actual, 'testNoEqualSighNoValueWhere');
    }

    public function test1NoEqualSighNoValueWhere(): void
    {
        $expected = \ArgumentCountError::class;
        $params = new Params(null);
        $this->expectException($expected, 'test1NoEqualSighNoValueWhere');
        $params->where();
        $actual = $params->data['data'];
    }
}