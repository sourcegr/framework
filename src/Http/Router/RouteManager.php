<?php


    namespace Sourcegr\Framework\Http\Router;


    class RouteManager
    {
        public $routeCollection;

        public function __construct()
        {
            $this->routeCollection = new RouteCollection();
        }

        public function loadRoutes(string $realm, callable $callback)
        {
            $this->routeCollection->onRealm($realm, $callback);
            return $this;
        }

        public function matchRoute(array $params)
        {
            $url = $params['url'];
            $realm = $params['realm'];
            $method = $params['method'];

            $activeRoutes = $this->routeCollection->filterRoutes($realm, $method);

            if (!$activeRoutes) {
                return null;
            }

            [$exactRoutes, $parameterRoutes] = $this->routeCollection->routesByType($activeRoutes);


            // try to match exact routes
            /** @var Route $route */
            foreach ($exactRoutes as $route) {
                if ($route->getCompiledParam('url') === $url) {
                    $matcher = new RouteMatch($route, null);
                    return $matcher;
                }
            }

            // search parameterized
            $matched = null;
            $urlParser = new URLRouteParser($url);


            foreach ($parameterRoutes as $route) {
                if ($matched = $urlParser->matches($route)) {
                    break;
                }
            }

            if (!$matched) {
                $matched = $this->routeCollection->getFourOhFour();
            }

            return $matched;
        }
    }