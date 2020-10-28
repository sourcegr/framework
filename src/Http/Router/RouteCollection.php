<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\Http\Router;


    use Sourcegr\Framework\Http\Boom;
    use Sourcegr\Framework\Http\BoomException;
    use Sourcegr\Framework\Http\Response\HTTPResponseCode;

    class RouteCollection implements RouteCollectionInterface
    {
        public const DEFAULT_REALM = 'WEB';

        protected $routes = [];
        protected $defaultRealm = self::DEFAULT_REALM;

        protected function callRouteMethod(string $memberMethod, string $param, callable $closure = null)
        {
            if (!$closure) {
                foreach ($this->routes as $route) {
                    $route->$memberMethod($param);
                }
                return;
            }

            $a = new static($this->defaultRealm);

            $closure($a);

            foreach ($a->routes as $route) {
                $route->$memberMethod($param);
            }

            $this->routes = $a->routes;
        }

        public function filterRoutes($realm, $method)
        {
            $matched = [];

            foreach ($this->routes as $route) {
                if ($realm === $route->getCompiledParam('realm') && in_array($method, $route->getCompiledParam('method'))) {
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

        public function getFourOhFour()
        {
            throw new BoomException(new Boom(HTTPResponseCode::HTTP_NOT_FOUND));
        }

        public function __construct(?string $defaultRealm = null)
        {
            $this->defaultRealm = $defaultRealm ?? self::DEFAULT_REALM;
        }

        public function addRoute(
            array $method,
            string $url,
            $callback,
            $callbackMethod = null,
            $predicate = null,
            string $middleware = null
        ): Route {
            if (is_string($callback) && is_string($callbackMethod)) {
                $callback = "$callback@$callbackMethod";
            }

            $r = new Route($this->defaultRealm, $method, $url, $callback, $predicate, $middleware);
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
            return $this->callRouteMethod('setPrefix', $basePath, $closure);
        }

        public function setMiddleware(string $middleware, callable $closure = null)
        {
            return $this->callRouteMethod('setMiddleware', $middleware, $closure);
        }

        public function setRealm(string $realm, callable $closure = null)
        {
            return $this->callRouteMethod('setRealm', $realm, $closure);
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

        public function rest($url, $controller) {
            $r = new RestfullRoute($this->defaultRealm, $controller, $url, null, null, null);
            $this->routes[] = $r;
            return $r;
        }

        /*
         * todo
         * add public function FourOhFour
         * add public function ? any more ?
         * add public function REDIRECT
         */
    }