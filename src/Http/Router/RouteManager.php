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
            $realm = $request->realm ?? '';
            $method = $request->method ?? null;

            $this->routeCollection->setRealm($realm, $callback);

            if (is_null($url) || is_null($realm) || is_null($method)) {
                throw new \Exception('url, realm and method should be provided in the Request object');
            }

            $activeRoutes = $this->routeCollection->filterRoutes($realm, $method);

            if (!$activeRoutes) {
                return $this->routeCollection->getFourOhFour();
            }

            [$exactRoutes, $parameterRoutes] = $this->routeCollection->routesByType($activeRoutes);


            /** @var Route $route */
            foreach ($exactRoutes as $route) {
                # try to match exact routes
                # we need to convert the matched to a RouteMatch!
                if ($route->getCompiledParam('url') === $url) {
                    $matched = new RouteMatch($route, null, null);
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