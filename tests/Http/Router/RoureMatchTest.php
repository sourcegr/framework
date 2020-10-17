<?php

    declare(strict_types=1);

    namespace Sourcegr\Tests\Http\Router;

    use Sourcegr\Framework\Http\Boom;
    use Sourcegr\Framework\Http\BoomException;
    use Sourcegr\Framework\Http\Router\Route;
    use PHPUnit\Framework\TestCase;
    use Sourcegr\Framework\Http\Router\RouteCollection;
    use Sourcegr\Framework\Http\Router\RouteManager;
    use Sourcegr\Framework\Http\Router\RouteMatch;
    use Sourcegr\Framework\Http\Router\URLRouteParser;

    class RoureMatchTest extends TestCase
    {
        const CURRENT_ROUTE = 'this/is/the/route';
        private $routes;

        private function noop()
        {
            return function () {
            };
        }
        private function init($url = null) {
            $this->routes = function (RouteCollection $routeCollection) {
                $routeCollection->GET(self::ROUTE, $this->noop());
                $routeCollection->POST(self::ROUTE, $this->noop());
                $routeCollection->PUT(self::ROUTE, $this->noop());
                $routeCollection->PATCH(self::ROUTE, $this->noop());
                $routeCollection->DELETE(self::ROUTE, $this->noop());
            };

            $request = new \Sourcegr\Framework\Http\Request\HttpRequest($url ?? '/');
            return new RouteManager($request);
        }



        public function testThrowsOnMultiOptional()
        {
            $manager = $this->init();
            $this->routes = function (RouteCollection $routeCollection) {
                $routeCollection->GET('get/?optional1/?optional2', $this->noop());
            };

            $this->expectException(\Exception::class, 'testThrowsOnMultiOptional');
            $manager->matchRoute($this->routes);
        }

        public function testDontMatchWithLessParts()
        {
            $manager = $this->init('contacts');
            $this->routes = function (RouteCollection $routeCollection) {
                $routeCollection->GET('contacts/#action', $this->noop());
            };

            $this->expectException(BoomException::class, 'testDontMatchWithLessParts');
            $manager->matchRoute($this->routes);
        }

        public function testDontMatchWithMoreParts()
        {
            $manager = $this->init('contacts/action/parameter');
            $this->routes = function (RouteCollection $routeCollection) {
                $routeCollection->GET('contacts/#action', $this->noop());
            };

            $this->expectException(BoomException::class, 'testDontMatchWithMoreParts');
            $manager->matchRoute($this->routes);
        }

        public function testDontMatchOnOptionalWithWayMoreParts()
        {
            $manager = $this->init('contacts/one/delete/1/2');
            $this->routes = function (RouteCollection $routeCollection) {
                $routeCollection->GET('/contacts/one/delete/?id', $this->noop());
            };

            $this->expectException(BoomException::class, 'testDontMatchOnOptionalWithWayMoreParts');
            $manager->matchRoute($this->routes);
        }

        public function testDontMatchOnOptionalWithLessParts()
        {
            $manager = $this->init('contacts/one');
            $this->routes = function (RouteCollection $routeCollection) {
                $routeCollection->GET('contacts/one/delete/?id', $this->noop());
            };

            $this->expectException(BoomException::class, 'testDontMatchOnOptionalWithLessParts');
            $manager->matchRoute($this->routes);
        }

        public function testMatchRequiredParameter()
        {
            $manager = $this->init('contacts/list');
            $this->routes = function (RouteCollection $routeCollection) {
                $routeCollection->GET('contacts/#action', $this->noop());
            };

            $actual = $manager->matchRoute($this->routes);

            $this->assertInstanceOf(RouteMatch::class, $actual);
            $this->assertIsArray($actual->vars, 'Expected vars to be Array');
            $this->assertCount(1, $actual->vars, 'Expected vars to have exactly one member');
            $this->assertArrayHasKey('action', $actual->vars);
            $this->assertEquals('list', $actual->vars['action']);
        }

        public function testMatchOptionalParameterNoWildcard()
        {
            $manager = $this->init('contacts/list');
            $this->routes = function (RouteCollection $routeCollection) {
                $routeCollection->GET('contacts/?action', $this->noop());
            };

            $actual = $manager->matchRoute($this->routes);

            $this->assertInstanceOf(RouteMatch::class, $actual);
            $this->assertIsArray($actual->vars, 'Expected vars to be Array');
            $this->assertCount(1, $actual->vars, 'Expected vars to have exactly one member');
            $this->assertArrayHasKey('action', $actual->vars);
            $this->assertEquals('list', $actual->vars['action']);
        }

        public function testMatchOptionalParameterNoWildcardEmpty()
        {
            $manager = $this->init('contacts');
            $this->routes = function (RouteCollection $routeCollection) {
                $routeCollection->GET('contacts/?action', $this->noop());
            };

            $actual = $manager->matchRoute($this->routes);
            $this->assertInstanceOf(RouteMatch::class, $actual);
            $this->assertIsArray($actual->vars, 'Expected vars to be Array');
            $this->assertCount(1, $actual->vars, 'Expected vars to have exactly one member');
            $this->assertArrayHasKey('action', $actual->vars);
            $this->assertEquals('', $actual->vars['action']);
        }

        public function testMatchOptionalParameterWithWildcard()
        {
            $manager = $this->init('contacts/too/long/url');
            $this->routes = function (RouteCollection $routeCollection) {
                $routeCollection->GET('contacts/?action', $this->noop())->matchesAll();
            };

            $actual = $manager->matchRoute($this->routes);

            $this->assertInstanceOf(RouteMatch::class, $actual);
            $this->assertIsArray($actual->vars, 'Expected vars to be Array');
            $this->assertCount(1, $actual->vars, 'Expected vars to have exactly one member');
            $this->assertArrayHasKey('action', $actual->vars);
            $this->assertEquals('too/long/url', $actual->vars['action']);
        }

        public function testMatchOptionalParameterWithWildcardEmpty()
        {
            $manager = $this->init('contacts');
            $this->routes = function (RouteCollection $routeCollection) {
                $routeCollection->GET('contacts/?action', $this->noop())->matchesAll();
            };

            $actual = $manager->matchRoute($this->routes);

            $this->assertInstanceOf(RouteMatch::class, $actual);
            $this->assertIsArray($actual->vars, 'Expected vars to be Array');
            $this->assertCount(1, $actual->vars, 'Expected vars to have exactly one member');
            $this->assertArrayHasKey('action', $actual->vars);
            $this->assertEquals('', $actual->vars['action']);
        }

        public function testDontMatchWithWhere()
        {
            $manager = $this->init('contacts/papas');
            $this->routes = function (RouteCollection $routeCollection) {
                $routeCollection->GET('contacts/?id', $this->noop())->where('id', '/^[1-9][0-9]$/');
            };

            $this->expectException(\Exception::class, 'testThrowsOnMultiOptional');
            $actual = $manager->matchRoute($this->routes);
        }
        public function testMatchWithWhere()
        {
            $manager = $this->init('contacts/4');
            $this->routes = function (RouteCollection $routeCollection) {
                $routeCollection->GET('contacts/?id', $this->noop())->where('id', '/^[1-9][0-9]*$/');
            };

//            $this->expectException(\Exception::class, 'testThrowsOnMultiOptional');
            $actual = $manager->matchRoute($this->routes);

            $this->assertInstanceOf(RouteMatch::class, $actual);
            $this->assertIsArray($actual->vars, 'Expected vars to be Array');
            $this->assertCount(1, $actual->vars, 'Expected vars to have exactly one member');
            $this->assertArrayHasKey('id', $actual->vars);
            $this->assertEquals(4, $actual->vars['id']);
        }


        public function testDontMatchWithPredicate()
        {
            $manager = $this->init('contacts/delete/ERROR');
            $this->routes = function (RouteCollection $routeCollection) {
                $routeCollection->GET('contacts/delete/?id', $this->noop())
                    ->setPredicate(function ($match) {
                        return $match->vars['id'] == 100;
                    });
            };

            $this->expectException(BoomException::class, 'testThrowsOnMultiOptional');
            $actual = $manager->matchRoute($this->routes);

//            $this->assertInstanceOf(Boom::class, $actual);
//            $this->assertEquals(404, $actual->statusCode, '404 should be returned');
        }

        public function testMatchWithPredicate()
        {
            $manager = $this->init('contacts/delete/100');
            $this->routes = function (RouteCollection $routeCollection) {
                $routeCollection->GET('contacts/delete/?id', $this->noop())
                    ->setPredicate(function ($match) {
                        return $match->vars['id'] == 100;
                    });
            };

//            $this->expectException(BoomException::class, 'testThrowsOnMultiOptional');
            $actual = $manager->matchRoute($this->routes);

            $this->assertInstanceOf(RouteMatch::class, $actual);
            $this->assertIsArray($actual->vars, 'Expected vars to be Array');
            $this->assertCount(1, $actual->vars, 'Expected vars to have exactly one member');
            $this->assertArrayHasKey('id', $actual->vars);
            $this->assertEquals(100, $actual->vars['id']);
        }

    }
