<?php

    declare(strict_types=1);

    namespace Sourcegr\Tests\Http\Router;

    use Sourcegr\Framework\Http\Router\Route;
    use PHPUnit\Framework\TestCase;

    class RouteTest extends TestCase
    {
        const ROUTE = 'this/is/the/route';

        private function init($url = self::ROUTE)
        {
            return new Route('REALM', 'GET', $url, $this->noop(), null, null);
        }

        private function noop()
        {
            return function () {
            };
        }

        public function testCreation()
        {
            $method = 'get';
            $url = '/this/is/the/route/';

            $route = new Route(null, $method, $url, $this->noop(), null, null);

            $actual = $route->getCompiledParam('method');
            $expected = [strtoupper($method)];
            $this->assertEquals($expected, $actual, 'METHOD failure');

            $actual = $route->getCompiledParam('url');
            $expected = 'this/is/the/route';
            $this->assertEquals($expected, $actual, 'URL failure');


            $actual = $route->getCompiledParam('predicates');
            $this->assertIsArray($actual, "Failure on predicates");

            $actual = $route->getCompiledParam('middlewares');
            $this->assertIsArray($actual, "Failure on middlewares");
        }

        public function testSetPrefix()
        {
            $route = $this->init('/dashboard');
            $route->setPrefix('/admin');

            $expected = 'admin/dashboard';
            $actual = $route->getCompiledParam('url');

            $this->assertEquals($expected, $actual, 'setPrefix failure');
        }

        public function testSetMiddleware()
        {
            $m = 'MIDDLEWARE';
            $route = $this->init('/dashboard');
            $route->setMiddleware($m);

            $expected = [$m];
            $actual = $route->getCompiledParam('middlewares');

            $this->assertEquals($expected, $actual, 'setMiddleware failure');
        }

        public function testSetMiddlewareNull()
        {
            $m = null;
            $route = $this->init('/dashboard');
            $route->setMiddleware($m);

            $expected = [];
            $actual = $route->getCompiledParam('middlewares');

            $this->assertEquals($expected, $actual, 'setMiddlewareNull failure');
        }

        public function testSetMiddlewareArray()
        {
            $m = ['MIDDLEWARE1', 'MIDDLEWARE2'];
            $route = $this->init('/dashboard');
            $route->setMiddleware($m);

            $expected = $m;
            $actual = $route->getCompiledParam('middlewares');

            $this->assertEquals($expected, $actual, 'setMiddlewareArray failure');
        }

        public function testSetMiddlewareAddMore()
        {
            $m1 = 'MIDDLEWARE1';
            $m2 = 'MIDDLEWARE2';

            $route = $this->init('/dashboard');

            $route->setMiddleware($m1);
            $route->setMiddleware($m2);

            $expected = ['MIDDLEWARE1', 'MIDDLEWARE2'];
            $actual = $route->getCompiledParam('middlewares');

            $this->assertEquals($expected, $actual, 'setMiddlewareAddMore failure');
        }


        public function testSetPredicate()
        {
            $pr = $this->noop();

            $route = $this->init('/dashboard');
            $route->setPredicate($pr);

            $expected = [$pr];
            $actual = $route->getCompiledParam('predicates');

            $this->assertEquals($expected, $actual, 'setPredicate failure');
        }

        public function testSetPredicateAddMore()
        {
            $pr = $this->noop();
            $p = [$pr, $pr];

            $route = $this->init('/dashboard');
            $route->setPredicate($pr);
            $route->setPredicate($pr);

            $expected = $p;
            $actual = $route->getCompiledParam('predicates');

            $this->assertEquals($expected, $actual, 'setPredicateAddMore failure');
        }

        public function testWhere() {
            $var = 'id';
            $check = '/^[1-9][0-9]*$/'; #number
            $route = $this->init('/users/#id');

            $route->where($var, $check);

            $actual = $route->getCompiledParam('where');
            $this->assertIsCallable($actual, 'where is not a function');

            /** @var callable $actual */
            $result = $actual([$var => '111']);
            $this->assertTrue($result, 'result of where should be true');

            $result = $actual([$var => 'fail']);
            $this->assertFalse($result, 'result of where should be false');
        }

    }
