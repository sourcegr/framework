<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\Http\Router;


    use Sourcegr\Framework\Base\Helpers\Arr;

    class RouteCollection
    {
        public $routes = [];
        protected $routeMap = [];
        protected $fourOhFour = null;
        protected $defaultRealm = '';

        private function callRouteMethod(string $memberMethod, string $basePath, callable $closure)
        {
            $a = new static($this->defaultRealm);

            $closure($a);

            foreach ($a->getRouteMap() as $route) {
                $route->$memberMethod($basePath);
            }

            $this->mergeRoutes($a->getRouteMap());
        }

        public function filterRoutes($realm, $method)
        {
            $matched = [];

            foreach ($this->routeMap as $route) {
                if ($realm === $route->getCompiledParam('realm') && $method === $route->getCompiledParam('method')) {
                    $matched[] = $route;
                }
            }

            return $matched;
        }

        public function routesByType(array $routes): array
        {
            $withParams = [];
            $withoutParams = [];


            /** @var Route $route */
            foreach ($routes as $route) {
                $url = $route->getCompiledParam('url');
                if (strpos($url, '#') !== false || strpos($url, '?') !== false) {
                    $withParams[$url] = $route;
                } else {
                    $withoutParams[$url] = $route;
                }
            }

            return [$withoutParams, $withParams];
        }

        /**
         * @return null
         */
        public function getFourOhFour()
        {
            return $this->fourOhFour;
        }

        public function __construct(?string $defaultRealm = null)
        {
            $this->defaultRealm = $defaultRealm ?? 'WEB';
        }

        protected function addRoute(
            string $method,
            string $url,
            $callback,
            $predicate = null,
            string $middleware = null
        ): Route {
            $r = new Route($method, $url, $callback, $predicate, $middleware);
//            $r->setCollection($this);
            $this->routeMap[] = $r;
            return $r;
        }

        public function compile()
        {
            if (!$this->routes) {
                foreach ($this->routeMap as $route) {
                    $realm = $route->realm ?? 'WEB';
                    $method = $route->method;

                    $this->routes[$realm][$method][] = $route;
                }
            }

            return $this->routes;
        }


        public function mergeRoutes(array $routes)
        {
            $this->routeMap = Arr::merge($this->routeMap, $routes);
            return $this;
        }

        public function getRouteMap()
        {
            return $this->routeMap;
        }


        // parameter setting
        public function setPrefix(string $basePath, callable $closure)
        {
            $this->callRouteMethod('setPrefix', $basePath, $closure);
            return $this;
        }

        public function setMiddleware(string $middleware, callable $closure)
        {
            $this->callRouteMethod('setMiddleware', $middleware, $closure);
            return $this;
        }

        public function onRealm(string $realm, callable $closure)
        {
            $this->callRouteMethod('setRealm', $realm, $closure);
            return $this;
        }


        //
        public function add($method, $url, $callback)
        {
            return $this->addRoute($method, $url, $callback);
        }

        public function GET($url, $callback)
        {
            return $this->addRoute('GET', $url, $callback);
        }

        public function POST($url, $callback)
        {
            return $this->addRoute('POST', $url, $callback);
        }

        /*
         * todo
         * add public function DELETE
         * add public function PATCH
         * add public function ? any more ?
         */
    }