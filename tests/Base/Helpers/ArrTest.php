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
            $array1 = [1, 2, 3];
            $array2 = [4, 5, 6];
            $actual = ARR::merge($array1, $array2);
            $expected = [1, 2, 3, 4, 5, 6];
            $this->assertSame($expected, $actual, "Failure on Arr::merge");
        }

        public function testArrayReplaceBothArrays() {
            $array1 = ['a' => 1, 'b' => 2, 'c' => 3];
            $array2 = ['a' => 4, 'b' => 5, 'c' => 6];
            $actual = ARR::arrayReplace($array1, $array2);
            $expected = $array2;
            $this->assertSame($expected, $actual, "Failure on Arr::arrayReplace");
        }
        public function testArrayReplaceBothArraysSome() {
            $array1 = ['a' => 1, 'b' => 2, 'c' => 3];
            $array2 = ['a' => 4, 'b' => 5, 'd' => 6];
            $actual = ARR::arrayReplace($array1, $array2);
            $expected = ['a' => 4, 'b' => 5, 'c'=> 3, 'd' => 6];
            $this->assertSame($expected, $actual, "Failure on Arr::arrayReplace");
        }


        public function testArrayReplaceBothArrayObjects() {
            $array1 = $this->getArrayObjectObject();
            $array2 = new \ArrayObject(['a' => 5, 'b' => 5, 'c' => 5]);
            $actual = ARR::arrayReplace($array1, $array2);
            $actual = ARR::getPureArray($actual);
            $expected = ['a' => 5, 'b' => 5, 'c' => 5];
            $this->assertSame($expected, $actual, "Failure on Arr::arrayReplace");
        }
        public function testArrayReplaceBothArrayObjectsSome() {
            $array1 = $this->getArrayObjectObject();
            $array2 = new \ArrayObject(['a' => 5, 'b' => 5, 'd' => 5]);
            $actual = ARR::arrayReplace($array1, $array2);
            $actual = ARR::getPureArray($actual);
            $expected = ['a' => 5, 'b' => 5, 'c' => 3, 'd' => 5];
            $this->assertSame($expected, $actual, "Failure on Arr::arrayReplace");
        }


        private function getArrayObject()
        {
            return new \ArrayObject([1, 2, 3]);
        }

        private function getArrayObjectObject()
        {
            return new \ArrayObject(['a' => 1, 'b' => 2, 'c' => 3]);
        }
    }
