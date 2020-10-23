<?php

    namespace Sourcegr\Tests\Http\Router;

    use Sourcegr\Framework\Http\Boom;
    use Sourcegr\Framework\Http\BoomException;
    use Sourcegr\Framework\Http\Request\HttpRequest;
    use Sourcegr\Framework\Http\Router\RouteCollection;
    use Sourcegr\Framework\Http\Router\RouteManager;
    use PHPUnit\Framework\TestCase;
    use Sourcegr\Framework\Http\Router\RouteMatch;

    class RouteManagerTest extends TestCase
    {
        private $routes;
        const ROUTE = 'this/is/the/route';

        private function noop()
        {
            return function () {
            };
        }

        private function init($url = '/')
        {
            $this->routes = function (RouteCollection $routeCollection) {
                $routeCollection->GET(self::ROUTE, $this->noop());
                $routeCollection->POST(self::ROUTE, $this->noop());
                $routeCollection->PUT(self::ROUTE, $this->noop());
                $routeCollection->PATCH(self::ROUTE, $this->noop());
                $routeCollection->DELETE(self::ROUTE, $this->noop());
            };

            $req = new HttpRequest($url);
            $manager = new RouteManager($req);
            return $manager;
        }


        public function testNoMatchStaticRoute()
        {
            $manager = $this->init();

            $actual = $manager->matchRoute($this->routes);
            $this->assertInstanceOf(Boom::class, $actual);
        }

        public function testMatchStaticRoute()
        {
            $manager = $this->init(static::ROUTE);
            $this->routes = function (RouteCollection $routeCollection) {
                $routeCollection->GET(static::ROUTE, $this->noop());
            };

//            $this->expectException(BoomException::class, 'testThrowsOnMultiOptional');
            $actual = $manager->matchRoute($this->routes);

            $this->assertInstanceOf(RouteMatch::class, $actual, 'Expected RouteMatch class');
        }

        public function testNoMatchParametersRoute()
        {
            $manager = $this->init('contacts/all');
            $this->routes = function (RouteCollection $routeCollection) {
                $routeCollection->GET('contacts/#var1/#var2', $this->noop());
            };

            $actual = $manager->matchRoute($this->routes);
            $this->assertInstanceOf(Boom::class, $actual);
        }


        public function testReturnsEmptyVarsOnStaticMatch()
        {
            $manager = $this->init(static::ROUTE);
            $this->routes = function (RouteCollection $routeCollection) {
                $routeCollection->GET(static::ROUTE, $this->noop());
            };

//            $this->expectException(BoomException::class, 'testThrowsOnMultiOptional');
            $actual = $manager->matchRoute($this->routes);

            $this->assertIsArray($actual->vars, 'Expected vars to be Array');
            $this->assertCount(0, $actual->vars, 'Expected vars to be EMPTY Array');
        }


        public function testReturnsMatcherOnParametersMatch()
        {
            $manager = $this->init('contacts/12/edit');
            $this->routes = function (RouteCollection $routeCollection) {
                $routeCollection->GET('contacts/#id/#action', $this->noop());
            };

//            $this->expectException(BoomException::class, 'testThrowsOnMultiOptional');
            $actual = $manager->matchRoute($this->routes);

            $this->assertInstanceOf(RouteMatch::class, $actual, 'Expected RouteMatch class');
            $this->assertIsArray($actual->vars, 'Expected vars to be Array');
            $this->assertCount(2, $actual->vars, 'Expected vars to have length 2');
            $this->assertEquals(12, $actual->vars['id'], 'id should be 12');
            $this->assertEquals('edit', $actual->vars['action'], 'action should be edit');
        }
    }
