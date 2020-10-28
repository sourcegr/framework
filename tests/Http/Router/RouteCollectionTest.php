<?php

    declare(strict_types=1);

    namespace Sourcegr\Tests\Http\Router;

    use Sourcegr\Framework\Http\Router\Route;
    use Sourcegr\Framework\Http\Router\RouteCollection;
    use PHPUnit\Framework\TestCase;

    class DebugRouteCollection extends RouteCollection {
        public function getProp($prop) {
            return $this->$prop;
        }
    }

    class RouteCollectionTest extends TestCase
    {
        const ROUTE = 'this/is/the/route';

        private function noop()
        {
            return function () {
            };
        }



        public function testMETHODS() {
            $coll = new DebugRouteCollection(null);
            $this->assertCount(0, $coll->getRoutes(), 'Initial routes are not empty');

            $coll->GET(self::ROUTE, $this->noop());
            $this->assertCount(1, $coll->getRoutes(), 'Route count should be 1');

            $coll->POST(self::ROUTE, $this->noop());
            $this->assertCount(2, $coll->getRoutes(), 'Route count should be 2');

            $coll->PUT(self::ROUTE, $this->noop());
            $this->assertCount(3, $coll->getRoutes(), 'Route count should be 3');

            $coll->PATCH(self::ROUTE, $this->noop());
            $this->assertCount(4, $coll->getRoutes(), 'Route count should be 4');

            $coll->DELETE(self::ROUTE, $this->noop());
            $this->assertCount(5, $coll->getRoutes(), 'Route count should be 5');


            /** @var Route $routeGET */
            $routeGET = $coll->getRoutes()[0];
            $expected = ['GET'];
            $this->assertEquals($expected, $routeGET->getCompiledParam('method'), 'Method should be GET');

            $expected = self::ROUTE;
            $this->assertEquals($expected, $routeGET->getCompiledParam('url'), 'URL should not be this');


            /** @var Route $routePOST */
            $routePOST = $coll->getRoutes()[1];
            $expected = ['POST'];
            $this->assertEquals($expected, $routePOST->getCompiledParam('method'), 'Method should be POST');

            /** @var Route $routePUT */
            $routePUT = $coll->getRoutes()[2];
            $expected = ['PUT'];
            $this->assertEquals($expected, $routePUT->getCompiledParam('method'), 'Method should be PUT');

            /** @var Route $routePATCH */
            $routePATCH = $coll->getRoutes()[3];
            $expected = ['PATCH'];
            $this->assertEquals($expected, $routePATCH->getCompiledParam('method'), 'Method should be PATCH');

            /** @var Route $routeDELETE */
            $routeDELETE = $coll->getRoutes()[4];
            $expected = ['DELETE'];
            $this->assertEquals($expected, $routeDELETE->getCompiledParam('method'), 'Method should be DELETE');
        }

        public function testDirectSetRouteParameters() {
            $coll = new DebugRouteCollection(null);
            $coll->GET(self::ROUTE, $this->noop());

            /** @var Route $route */
            $route = $coll->getRoutes()[0];

            $coll->setPrefix('/ROOT/');
            $expected = 'ROOT/' . self::ROUTE;
            $this->assertEquals($expected, $route->getCompiledParam('url'), 'URL should not be this when setPrefix');

            $coll->setMiddleware('new middleware');
            $expected = ['new middleware'];
            $this->assertEquals($expected, $route->getCompiledParam('middlewares'), 'Middlewares should not be this when setMiddleware');

            $coll->setMiddleware('one more middleware');
            $expected = ['new middleware', 'one more middleware'];
            $this->assertEquals($expected, $route->getCompiledParam('middlewares'), 'Middlewares should not be this when setMiddleware');
        }

        public function testCallbackSetRouteParameters() {
            $coll = new DebugRouteCollection(null);

            $coll->setPrefix('ROOT', function(RouteCollection $routeCollection) {
                $routeCollection->GET(self::ROUTE, $this->noop());
            });

            /** @var Route $route */
            $route = $coll->getRoutes()[0];

            $expected = 'ROOT/' . self::ROUTE;
            $this->assertEquals($expected, $route->getCompiledParam('url'), 'middleware should not be this when setMiddleware');

            #middleware
            $coll->setMiddleware('new middleware', function(RouteCollection $routeCollection) {
                $routeCollection->GET(self::ROUTE, $this->noop());
            });
            $route = $coll->getRoutes()[0];

            $expected = ['new middleware'];
            $this->assertEquals($expected, $route->getCompiledParam('middlewares'), 'Middlewares should not be this when setMiddleware');

        }


        public function testRouteByType()
        {
            $coll = new DebugRouteCollection(null);

            $coll->GET('static1', $this->noop());
            $coll->GET('static2/more', $this->noop());
            $coll->GET('static3/more/links', $this->noop());
            $coll->GET('dynamic1/#required_param', $this->noop());
            $coll->GET('dynamic2/#required_param/?optional_param', $this->noop());

            [$apiroutes, $withoutParams, $withParams] = $coll->routesByType();
            $this->assertCount(3, $withoutParams, 'withoutParams is wrong');
            $this->assertCount(2, $withParams, 'withParams is wrong');


            $this->assertArrayHasKey('static3/more/links', $withoutParams);
            $this->assertArrayHasKey('static2/more', $withoutParams);
            $this->assertArrayHasKey('static1', $withoutParams);

            $this->assertArrayHasKey('dynamic1/#required_param', $withParams);
            $this->assertArrayHasKey('dynamic2/#required_param/?optional_param', $withParams);
        }

        public function testFilterBy()
        {
            $coll = new DebugRouteCollection(null);

            $routeGet1 = $coll->GET('static1', $this->noop());
            $routeGet2 = $coll->GET('static2', $this->noop());
            $routePost1 = $coll->POST('static3', $this->noop());

            // the bellow SHOULD be filtered out
            $coll->PUT('dynamic1/#required_param', $this->noop());
            $coll->PUT('dynamic2/#required_param/?optional_param', $this->noop());


            $GET = $coll->filterRoutes('GET');
            $POST = $coll->filterRoutes('POST');

            $this->assertCount(2, $GET, 'GET count should be 2');
            $this->assertCount(1, $POST, 'POST count should be 1');

            $this->assertContains($routeGet1, $GET, 'GET should contain routeGet1');
            $this->assertContains($routeGet2, $GET, 'GET should contain routeGet2');
            $this->assertContains($routePost1, $POST, 'POST should contain routePost1');
        }
    }

