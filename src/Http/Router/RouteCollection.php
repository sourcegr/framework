<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\Http\Router;


    use Sourcegr\Framework\Http\Boom;
    use Sourcegr\Framework\Http\BoomException;
    use Sourcegr\Framework\Http\Response\HTTPResponseCode;

    class RouteCollection implements RouteCollectionInterface
    {
        protected $routes = [];

        public function __construct(callable $creationCallback = null)
        {
            if (is_callable($creationCallback)) {
                $creationCallback($this);
            }
        }

        protected function callRouteMethod(string $memberMethod, string $param, callable $closure = null)
        {
            if (!$closure) {
                foreach ($this->routes as $route) {
                    $route->$memberMethod($param);
                }
                return;
            }

            $a = new static();

            $closure($a);

            foreach ($a->routes as $route) {
                $this->routes[] = $route->$memberMethod($param);
            }
        }

        public function filterRoutes($method)
        {
            $matched = [];

            foreach ($this->routes as $route) {
                if (in_array($method, $route->getCompiledParam('method'))) {
                    $matched[] = $route;
                }
            }

            return $matched;
        }

        public function routesByType(array $routes = null): array
        {
            $routes = $routes ?? $this->routes;
            $withParams = [];
            $withoutParams = [];
            $restfull = [];


            /** @var Route $route */
            foreach ($routes as $route) {
                $url = $route->getCompiledParam('url');

                if ($route instanceof RestfullRoute) {
                    $restfull[$url] = $route;
                    continue;
                }

                if (strpos($url, '#') !== false || strpos($url, '?') !== false) {
                    $withParams[$url] = $route;
                    continue;
                }

                $withoutParams[$url] = $route;
            }

            return [$restfull, $withoutParams, $withParams];
        }

        public function addRoute(
            array $method,
            string $url,
            $callback,
            $callbackMethod = null,
            $predicates = null,
            $middleware = null
        ): Route {
            if (is_string($callback) && is_string($callbackMethod)) {
                $callback = "$callback@$callbackMethod";
            }

            $r = new Route($method, $url, $callback, $predicates, $middleware);
//            $r->setCollection($this);
            $this->routes[] = $r;
            return $r;
        }
//
//        public function mergeRoutes(array $routes)
//        {
//            $this->routes = Arr::merge($this->routes, $routes);
//            return $this;
//        }

        public function getRoutes()
        {
            return $this->routes;
        }

        // parameter setting
        public function setPrefix(string $basePath, callable $closure = null)
        {
            $this->callRouteMethod('setPrefix', $basePath, $closure);
            return $this;
        }

        public function setMiddleware(string $middleware, callable $closure = null)
        {
            $this->callRouteMethod('setMiddleware', $middleware, $closure);
            return $this;
        }

        public function setPredicate(callable $predicate = null)
        {
            foreach ($this->routes as $route){
                $route->setPredicate($predicate);
            }
            return $this;
        }


        // methods and basic
        public function GET($url, $callback, $method = null)
        {
            return $this->addRoute(['GET'], $url, $callback, $method);
        }

        public function POST($url, $callback, $method = null)
        {
            return $this->addRoute(['POST'], $url, $callback, $method);
        }

        public function PUT($url, $callback, $method = null)
        {
            return $this->addRoute(['PUT'], $url, $callback, $method);
        }

        public function PATCH($url, $callback, $method = null)
        {
            return $this->addRoute(['PATCH'], $url, $callback, $method);
        }

        public function DELETE($url, $callback, $method = null)
        {
            return $this->addRoute(['DELETE'], $url, $callback, $method);
        }

        public function rest($url, $controller, $callback) {
            $rr = new RestfullRoute($url, $controller);
            $callback($rr);
            $predicates = $rr->getPredicates();
            $middleware = $rr->getMiddleware();
            foreach ($rr->getCompiledRoutes() as $route) {
                $route[] = $predicates;
                $route[] = $middleware;
                $this->addRoute(...$route);
            }
            return $this;
        }

        /*
         * todo
         * add public function FourOhFour
         * add public function ? any more ?
         * add public function REDIRECT
         */
    }