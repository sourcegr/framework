<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\Http\Router;


    use Sourcegr\Framework\Http\Boom;
    use Sourcegr\Framework\Http\Request\RequestInterface;
    use Sourcegr\Framework\Http\Response\HTTPResponseCode;

    class RouteManager implements RouteManagerInterface
    {
        public $routeCollection;
        protected $request;
        protected $predicateCompiler;

        protected function orderByVars(array $parameterRoutes)
        {
//            return $parameterRoutes;
            $ordered = [];

            foreach ($parameterRoutes as $route) {
                $routeSegments = $route->getRouteSegments();
                $position = $this->getVariablePosition($routeSegments);
                $ordered[$position][] = $route;
            }

            krsort($ordered, SORT_NUMERIC);
            return array_merge(...$ordered);
        }

        protected function getVariablePosition(array $routeSegments)
        {
            foreach ($routeSegments as $index => $segment) {
                if (strpos($segment, '#') !== false) {
                    return $index;
                }
                if (strpos($segment, '?') !== false) {
                    return $index;
                }
            }
            return 0;
        }

        public function __construct(RequestInterface $request, PredicateCompilerInterface $predicateCompiler = null)
        {
            $this->routeCollection = new RouteCollection();
            $this->request = $request;
            $this->predicateCompiler = $predicateCompiler ?? (new PredicateCompiler());
        }

        public function matchRoute(callable $callback)
        {
            $request = (object)$this->request;

            $url = $request->url ?? '/';
            $method = $request->method ?? null;

            if (is_null($url) || is_null($method)) {
                throw new \Exception('url and method should be provided in the Request object');
            }
            $this->routeCollection = new RouteCollection($callback);

            $activeRoutes = $this->routeCollection->filterRoutes($method);

            if (!$activeRoutes) {
                return new Boom(HTTPResponseCode::HTTP_NOT_FOUND);
            }

            [$restfullRoutes, $exactRoutes, $parameterRoutes] = $this->routeCollection->routesByType($activeRoutes);

            // first check for restfull controllers
            /** @var RestfullRoute $route */
//            foreach ($restfullRoutes as $route) {
//                if (strpos($route->getCompiledParam('url'), $url) === 0) {
//                    if ($route->hasMethod($method)) {
//
//                    }
//                }
//            }


            /** @var Route $route */
            foreach ($exactRoutes as $route) {
                # try to match exact routes
                # we need to convert the matched to a RouteMatch!
                if ($route->getCompiledParam('url') === $url) {
                    $matched = new RouteMatch($route, null, $this->predicateCompiler);
                    $matched->url = $url;
                    return $matched;
                }
            }



            if (count($parameterRoutes)) {
                # search parameterized
                $urlParser = new URLRouteParser($url);
                $parameterRoutes = $this->orderByVars($parameterRoutes);
                foreach ($parameterRoutes as $route) {
                    if ($matched = $urlParser->matches($route, $this->predicateCompiler)) {
                        return $matched;
                    }
                }
            }

            return new Boom(HTTPResponseCode::HTTP_NOT_FOUND);
        }
    }