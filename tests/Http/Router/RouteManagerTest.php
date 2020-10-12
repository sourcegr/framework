<?php

    namespace Sourcegr\Tests\Http\Router;

    use Sourcegr\Framework\Http\Boom;
    use Sourcegr\Framework\Http\Router\RouteCollection;
    use Sourcegr\Framework\Http\Router\RouteManager;
    use PHPUnit\Framework\TestCase;
    use Sourcegr\Framework\Http\Router\RouteMatch;

    class RouteManagerTest extends TestCase
    {
        const ROUTE = 'this/is/the/route';

        private function noop()
        {
            return function () {
            };
        }

        private function init()
        {
            $callBack = function (RouteCollection $routeCollection) {
                $routeCollection->GET(self::ROUTE, $this->noop());
                $routeCollection->POST(self::ROUTE, $this->noop());
                $routeCollection->PUT(self::ROUTE, $this->noop());
                $routeCollection->PATCH(self::ROUTE, $this->noop());
                $routeCollection->DELETE(self::ROUTE, $this->noop());
            };

            $manager = new RouteManager();
            $manager->loadRoutes('WEB', $callBack);
            return $manager;
        }

        public function testLoadsRoutes()
        {
            $allRoutes = [];
            $callBack = function (RouteCollection $routeCollection) use (&$allRoutes) {
                $allRoutes[] = $routeCollection->GET(self::ROUTE, $this->noop());
                $allRoutes[] = $routeCollection->POST(self::ROUTE, $this->noop());
                $allRoutes[] = $routeCollection->PUT(self::ROUTE, $this->noop());
                $allRoutes[] = $routeCollection->PATCH(self::ROUTE, $this->noop());
                $allRoutes[] = $routeCollection->DELETE(self::ROUTE, $this->noop());
            };

            $manager = new RouteManager();
            $manager->loadRoutes('WEB', $callBack);
            $actual = $manager->routeCollection->getRoutes();

            $this->assertCount(5, $actual, '5 Routes should have been created');
            $this->assertEquals($allRoutes, $actual, '5 Routes should have been created');
        }

        public function testNoMatchStaticRoute()
        {
            $manager = $this->init();

            $actual = $manager->matchRoute([
                'url' => 'WRONG',
                'realm' => 'WRONG',
                'method' => 'WRONG',
            ]);

            $this->assertInstanceOf(Boom::class, $actual, 'Should be Instance of Boom');
        }

        public function testMatchStaticRoute()
        {
            $manager = $this->init();

            $actual = $manager->matchRoute([
                'url' => self::ROUTE,
                'realm' => 'WEB',
                'method' => 'GET',
            ]);

            $this->assertInstanceOf(RouteMatch::class, $actual, 'Expected RouteMatch class');
//            $this->assertInstanceOf(Boom::class, $actual, 'Should be Instance of Boom');
        }

        public function testNoMatchParametersRoute()
        {
            $manager = $this->init();
            $manager->routeCollection->GET('contacts/#id/#action', $this->noop());

            $actual = $manager->matchRoute([
                'url' => 'WRONG',
                'realm' => 'WEB',
                'method' => 'GET',
            ]);

            $this->assertInstanceOf(Boom::class, $actual, 'Should be Instance of Boom');
        }

        public function testThrowsOnNoParams()
        {
            $manager = $this->init();

            $this->expectException(\Exception::class, 'It should throw');
            $manager->matchRoute([]);
        }

        public function testThrowsOnMissingParams()
        {
            $manager = $this->init();

            $this->expectException(\Exception::class, 'It should throw');
            $manager->matchRoute([
                'url' => 'WRONG',
                'realm' => 'WRONG',
            ]);
        }

        public function testReturnsMatcherOnStaticMatch()
        {
            $manager = $this->init();

            $actual = $manager->matchRoute([
                'url' => self::ROUTE,
                'method' => 'GET',
                'realm' => 'WEB',
            ]);

            $this->assertInstanceOf(RouteMatch::class, $actual, 'Expected RouteMatch class');
        }

        public function testReturnsEmptyVarsOnStaticMatch()
        {
            $manager = $this->init();

            $actual = $manager->matchRoute([
                'url' => self::ROUTE,
                'method' => 'GET',
                'realm' => 'WEB',
            ]);

            $this->assertIsArray($actual->vars, 'Expected vars to be Array');
            $this->assertCount(0, $actual->vars, 'Expected vars to be EMPTY Array');
        }


        public function testReturnsMatcherOnParametersMatch()
        {
            $manager = $this->init();
            $manager->routeCollection->GET('contacts/#id/#action', $this->noop());

            $actual = $manager->matchRoute([
                'url' => 'contacts/1/edit',
                'method' => 'GET',
                'realm' => 'WEB',
            ]);

            $this->assertInstanceOf(RouteMatch::class, $actual, 'Expected RouteMatch class');
        }

        public function testReturnsSomeVarsOnParametersMatch()
        {
            $manager = $this->init();
            $manager->routeCollection->GET('contacts/#id/#action', $this->noop());

            $actual = $manager->matchRoute([
                'url' => 'contacts/1/edit',
                'method' => 'GET',
                'realm' => 'WEB',
            ]);

            $this->assertIsArray($actual->vars, 'Expected vars to be Array');
            $this->assertCount(2, $actual->vars, 'Expected vars to has two keys Array');
        }

    }
