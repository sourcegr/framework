<?php

    declare(strict_types=1);


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
            $this->routeCollection->setRealm($realm, $callback);
            return $this;
        }

        public function matchRoute(array $params)
        {
            $url = $params['url'] ?? null;
            $realm = $params['realm'] ?? null;
            $method = $params['method'] ?? null;

            if (is_null($url) ||is_null($realm) ||is_null($method)) {
                throw new \Exception('url, realm and method should be provided as an associative array');
            }
            $activeRoutes = $this->routeCollection->filterRoutes($realm, $method);

            if (!$activeRoutes) {
                return $this->routeCollection->getFourOhFour();
            }

            [$exactRoutes, $parameterRoutes] = $this->routeCollection->routesByType($activeRoutes);


            # try to match exact routes
            # we need to convert the matched to a RouteMatch!
            /** @var Route $route */
            foreach ($exactRoutes as $route) {
                if ($route->getCompiledParam('url') === $url) {
                    $matcher = new RouteMatch($route, null);
                    return $matcher;
                }
            }

            # search parameterized
            # from now on, tthe $match is instance of RouteMatch
            # and we will return it as-is
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