<?php

    namespace Sourcegr\Tests\Base\Helpers;

    use Sourcegr\Framework\Base\Helpers\Arr;
    use PHPUnit\Framework\TestCase;

    class ArrTest extends TestCase
    {
        public function testEnsureArray()
        {
            $array = [1, 2, 3];
            $actual = ARR::ensureArray($array);
            $expected = $array;
            $this->assertSame($expected, $actual, "Failure on Arr::ensureArray");
        }

        public function testGetPureArray()
        {
            $array = $this->getArrayObject();
            $actual = ARR::getPureArray($array);
            $expected = [1, 2, 3];
            $this->assertSame($expected, $actual, "Failure on Arr::getPureArray");
        }

        public function testIs()
        {
            $array = [1, 2, 3];
            $actual = ARR::is($array);
            $expected = true;
            $this->assertSame($expected, $actual, "Failure on Arr::is");

            $array = $this->getArrayObject();
            $actual = ARR::is($array);
            $expected = true;
            $this->assertSame($expected, $actual, "Failure on Arr::is");

            $array = 'Not really an array';
            $array = null;
            $actual = ARR::is($array);
            $expected = false;
            $this->assertSame($expected, $actual, "Failure on Arr::is");
        }

        public function testIsArray()
        {
            $array = [1, 2, 3];
            $actual = ARR::isArray($array);
            $expected = true;
            $this->assertSame($expected, $actual, "Failure on Arr::isArray");

            $array = $this->getArrayObject();
            $actual = ARR::isArray($array);
            $expected = false;
            $this->assertSame($expected, $actual, "Failure on Arr::isArray");
        }

        public function testIsArrayObject()
        {
            $array = $this->getArrayObject();
            $actual = ARR::isArray($array);
            $expected = false;
            $this->assertSame($expected, $actual, "Failure on Arr::isArray");

            $array = [1, 2, 3];
            $actual = ARR::isArray($array);
            $expected = true;
            $this->assertSame($expected, $actual, "Failure on Arr::isArray");

        }

        public function testKeys()
        {
            $array = ['one'=>1, 'two'=>2, 'three'=>3];
            $actual = ARR::keys($array);
            $expected = ['one', 'two', 'three'];
            $this->assertSame($expected, $actual, "Failure on Arr::keys");

            $array = ['one', 'two', 'three'];
            $actual = ARR::keys($array);
            $expected = [0, 1, 2];
            $this->assertSame($expected, $actual, "Failure on Arr::keys");

            $array = ['one', 'b' => 'two', 'three'];
            $actual = ARR::keys($array);
            $expected = [0, 'b', 1];
            $this->assertSame($expected, $actual, "Failure on Arr::keys");
        }

        public function testValues()
        {
            $array = ['one'=>1, 'two'=>2, 'three'=>3];
            $actual = ARR::values($array);
            $expected = [1, 2, 3];
            $this->assertSame($expected, $actual, "Failure on Arr::values");

            $array = ['one', 'two', 'three'];
            $actual = ARR::values($array);
            $expected = ['one', 'two', 'three'];

            $this->assertSame($expected, $actual, "Failure on Arr::values");

            $array = ['one', 'b' => 'two', 'three'];
            $actual = ARR::values($array);
            $expected = ['one', 'two', 'three'];
            $this->assertSame($expected, $actual, "Failure on Arr::values");
        }

        public function testArrayReplace()
        {
            $array = [1, 2, 3];
            $newArray = ['a', 'b', 'c'];
            $actual = ARR::arrayReplace($array, $newArray);
            $expected = ['a', 'b', 'c'];
            $this->assertSame($expected, $actual, "Failure on Arr::arrayReplace");

            $array = [1, 2, 3];
            $newArray = null;
            $actual = ARR::arrayReplace($array, $newArray);
            $expected = [1, 2, 3];
            $this->assertSame($expected, $actual, "Failure on Arr::arrayReplace");

            $array = null;
            $newArray = [1, 2, 3];
            $actual = ARR::arrayReplace($array, $newArray);
            $expected = null;
            $this->assertSame($expected, $actual, "Failure on Arr::arrayReplace");
        }

        public function testMerge()
        {
            $array = [1, 2, 3];
            $actual = ARR::merge($array);
            $expected = 0000;
            $this->assertSame($expected, $actual, "Failure on Arr::merge");
        }

        public function testHas()
        {
            $array = [1, 2, 3];
            $actual = ARR::has($array);
            $expected = 0000;
            $this->assertSame($expected, $actual, "Failure on Arr::has");
        }

        private function getArrayObject()
        {
            return new \ArrayObject([1, 2, 3]);
        }
    }
