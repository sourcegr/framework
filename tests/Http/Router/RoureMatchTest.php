<?php

    declare(strict_types=1);

    namespace Sourcegr\Tests\Http\Router;

    use Sourcegr\Framework\Http\Boom;
    use Sourcegr\Framework\Http\Router\Route;
    use PHPUnit\Framework\TestCase;
    use Sourcegr\Framework\Http\Router\RouteCollection;
    use Sourcegr\Framework\Http\Router\RouteManager;
    use Sourcegr\Framework\Http\Router\RouteMatch;
    use Sourcegr\Framework\Http\Router\URLRouteParser;

    class RoureMatchTest extends TestCase
    {
        const CURRENT_ROUTE = 'this/is/the/route';

        private function noop()
        {
            return function () {
            };
        }



        public function testThrowsOnMultiOptional()
        {
            $callBack = function (RouteCollection $routeCollection) {
                $routeCollection->GET('/papas/?optional1/?optional2', $this->noop());
            };

            $manager = new RouteManager();

            $manager->loadRoutes('WEB', $callBack);
            $this->expectException(\Exception::class, 'Multiple optional should throw');
            $actual = $manager->matchRoute([
                'url' => 'papas',
                'realm' => 'WEB',
                'method' => 'GET',
            ]);
        }

        public function testDontMatchWithLessParts()
        {
            $callBack = function (RouteCollection $routeCollection) {
                $routeCollection->GET('/contacts/#action', $this->noop());
            };

            $manager = new RouteManager();

            $manager->loadRoutes('WEB', $callBack);
            $actual = $manager->matchRoute([
                'url' => 'contacts',
                'realm' => 'WEB',
                'method' => 'GET',
            ]);

            $this->assertInstanceOf(Boom::class, $actual);
            $this->assertEquals(404, $actual->statusCode, '404 should be returned');
        }

        public function testDontMatchWithMoreParts()
        {
            $callBack = function (RouteCollection $routeCollection) {
                $routeCollection->GET('/contacts/#action', $this->noop());
            };

            $manager = new RouteManager();

            $manager->loadRoutes('WEB', $callBack);
            $actual = $manager->matchRoute([
                'url' => 'contacts/delete/3',
                'realm' => 'WEB',
                'method' => 'GET',
            ]);

            $this->assertInstanceOf(Boom::class, $actual);
            $this->assertEquals(404, $actual->statusCode, '404 should be returned');
        }

        public function testDontMatchOnOptionalWithWayMoreParts()
        {
            $callBack = function (RouteCollection $routeCollection) {
                $routeCollection->GET('/contacts/one/delete/?id', $this->noop());
            };

            $manager = new RouteManager();

            $manager->loadRoutes('WEB', $callBack);
            $actual = $manager->matchRoute([
                'url' => 'contacts/one/delete/1/2',
                'realm' => 'WEB',
                'method' => 'GET',
            ]);

            $this->assertInstanceOf(Boom::class, $actual);
            $this->assertEquals(404, $actual->statusCode, '404 should be returned');
        }

        public function testDontMatchOnOptionalWithLessParts()
        {
            $callBack = function (RouteCollection $routeCollection) {
                $routeCollection->GET('/contacts/one/delete/?id', $this->noop());
            };

            $manager = new RouteManager();

            $manager->loadRoutes('WEB', $callBack);
            $actual = $manager->matchRoute([
                'url' => 'contacts/one',
                'realm' => 'WEB',
                'method' => 'GET',
            ]);

            $this->assertInstanceOf(Boom::class, $actual);
            $this->assertEquals(404, $actual->statusCode, '404 should be returned');
        }

        public function testMatchRequiredParameter()
        {
            $callBack = function (RouteCollection $routeCollection) {
                $routeCollection->GET('/contacts/#action', $this->noop());
            };

            $manager = new RouteManager();

            $manager->loadRoutes('WEB', $callBack);
            $actual = $manager->matchRoute([
                'url' => 'contacts/list',
                'realm' => 'WEB',
                'method' => 'GET',
            ]);

            $this->assertInstanceOf(RouteMatch::class, $actual);
            $this->assertIsArray($actual->vars, 'Expected vars to be Array');
            $this->assertCount(1, $actual->vars, 'Expected vars to have exactly one member');
            $this->assertArrayHasKey('action', $actual->vars);
            $this->assertEquals('list', $actual->vars['action']);
//            $this->assertEquals(404, $actual->statusCode, 'RouteMatch should be returned');
        }

        public function testMatchOptionalParameterNoWildcard()
        {
            $callBack = function (RouteCollection $routeCollection) {
                $routeCollection->GET('/contacts/?action', $this->noop());
            };

            $manager = new RouteManager();

            $manager->loadRoutes('WEB', $callBack);
            $actual = $manager->matchRoute([
                'url' => 'contacts/list',
                'realm' => 'WEB',
                'method' => 'GET',
            ]);

            $this->assertInstanceOf(RouteMatch::class, $actual);
            $this->assertIsArray($actual->vars, 'Expected vars to be Array');
            $this->assertCount(1, $actual->vars, 'Expected vars to have exactly one member');
            $this->assertArrayHasKey('action', $actual->vars);
            $this->assertEquals('list', $actual->vars['action']);
        }

        public function testMatchOptionalParameterNoWildcardEmpty()
        {
            $callBack = function (RouteCollection $routeCollection) {
                $routeCollection->GET('/contacts/?action', $this->noop());
            };

            $manager = new RouteManager();

            $manager->loadRoutes('WEB', $callBack);
            $actual = $manager->matchRoute([
                'url' => 'contacts',
                'realm' => 'WEB',
                'method' => 'GET',
            ]);

            $this->assertInstanceOf(RouteMatch::class, $actual);
            $this->assertIsArray($actual->vars, 'Expected vars to be Array');
            $this->assertCount(1, $actual->vars, 'Expected vars to have exactly one member');
            $this->assertArrayHasKey('action', $actual->vars);
            $this->assertEquals('', $actual->vars['action']);
        }

        public function testMatchOptionalParameterWithWildcard()
        {
            $callBack = function (RouteCollection $routeCollection) {
                $routeCollection->GET('/contacts/?action', $this->noop())->matchesAll();
            };

            $manager = new RouteManager();

            $manager->loadRoutes('WEB', $callBack);
            $actual = $manager->matchRoute([
                'url' => 'contacts/too/long/url',
                'realm' => 'WEB',
                'method' => 'GET',
            ]);

            $this->assertInstanceOf(RouteMatch::class, $actual);
            $this->assertIsArray($actual->vars, 'Expected vars to be Array');
            $this->assertCount(1, $actual->vars, 'Expected vars to have exactly one member');
            $this->assertArrayHasKey('action', $actual->vars);
            $this->assertEquals('too/long/url', $actual->vars['action']);
        }

        public function testMatchOptionalParameterWithWildcardEmpty()
        {
            $callBack = function (RouteCollection $routeCollection) {
                $routeCollection->GET('/contacts/?action', $this->noop())->matchesAll();
            };

            $manager = new RouteManager();

            $manager->loadRoutes('WEB', $callBack);
            $actual = $manager->matchRoute([
                'url' => 'contacts',
                'realm' => 'WEB',
                'method' => 'GET',
            ]);

            $this->assertInstanceOf(RouteMatch::class, $actual);
            $this->assertIsArray($actual->vars, 'Expected vars to be Array');
            $this->assertCount(1, $actual->vars, 'Expected vars to have exactly one member');
            $this->assertArrayHasKey('action', $actual->vars);
            $this->assertEquals('', $actual->vars['action']);
        }

        public function testDontMatchWithWhere()
        {
            $callBack = function (RouteCollection $routeCollection) {
                $routeCollection
                    ->POST('/contacts/delete/?id', $this->noop())
                    ->where('id', '/^[1-9][0-9]$/'); #(num with no leading zero)
            };

            $manager = new RouteManager();

            $manager->loadRoutes('WEB', $callBack);
            $actual = $manager->matchRoute([
                'url' => 'contacts/delete/ERROR',
                'realm' => 'WEB',
                'method' => 'POST',
            ]);

            $this->assertInstanceOf(Boom::class, $actual);
            $this->assertEquals(404, $actual->statusCode, '404 should be returned');
        }

        public function testMatchWithWhere()
        {
            $callBack = function (RouteCollection $routeCollection) {
                $routeCollection
                    ->POST('/contacts/delete/?id', $this->noop())
                    ->where('id', '/^[1-9][0-9]*$/'); #(num with no leading zero)
            };

            $manager = new RouteManager();

            $manager->loadRoutes('WEB', $callBack);
            $actual = $manager->matchRoute([
                'url' => 'contacts/delete/4',
                'realm' => 'WEB',
                'method' => 'POST',
            ]);

            $this->assertInstanceOf(RouteMatch::class, $actual);
            $this->assertIsArray($actual->vars, 'Expected vars to be Array');
            $this->assertCount(1, $actual->vars, 'Expected vars to have exactly one member');
            $this->assertArrayHasKey('id', $actual->vars);
            $this->assertEquals(4, $actual->vars['id']);
        }

        public function testDontMatchWithPredicate()
        {
            $callBack = function (RouteCollection $routeCollection) {
                $routeCollection
                    ->POST('/contacts/delete/?id', $this->noop())
                    ->setPredicate(function ($match) {
                        return $match->vars['id'] == 100;
                    });
            };

            $manager = new RouteManager();

            $manager->loadRoutes('WEB', $callBack);
            $actual = $manager->matchRoute([
                'url' => 'contacts/delete/ERROR',
                'realm' => 'WEB',
                'method' => 'POST',
            ]);

            $this->assertInstanceOf(Boom::class, $actual);
            $this->assertEquals(404, $actual->statusCode, '404 should be returned');
        }

        public function testMatchWithPredicate()
        {
            $callBack = function (RouteCollection $routeCollection) {
                $routeCollection
                    ->POST('/contacts/delete/?id', $this->noop())
                    ->setPredicate(function ($match) {
                        return $match->vars['id'] == 4;
                    });
            };

            $manager = new RouteManager();

            $manager->loadRoutes('WEB', $callBack);
            $actual = $manager->matchRoute([
                'url' => 'contacts/delete/4',
                'realm' => 'WEB',
                'method' => 'POST',
            ]);

            $this->assertInstanceOf(RouteMatch::class, $actual);
            $this->assertIsArray($actual->vars, 'Expected vars to be Array');
            $this->assertCount(1, $actual->vars, 'Expected vars to have exactly one member');
            $this->assertArrayHasKey('id', $actual->vars);
            $this->assertEquals(4, $actual->vars['id']);
        }

    }
