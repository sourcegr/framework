<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\Http\Router;



    use Sourcegr\Framework\Http\Request\RequestInterface;

    class RouteManager implements RouteManagerInterface
    {
        public $routeCollection;
        protected $request;
        protected $predicateCompiler;

        public function __construct(RequestInterface $request, PredicateCompilerInterface $predicateCompiler = null)
        {
            $this->routeCollection = new RouteCollection();
            $this->request = $request;
            $this->predicateCompiler = $predicateCompiler ?? (new PredicateCompiler());
        }

        public function matchRoute(callable $callback)
        {
            $request = (Object) $this->request;

            $url = $request->url ?? '/';
            $realm = $request->realm ?? '';
            $method = $request->method ?? null;

            $this->routeCollection->setRealm($realm, $callback);

            if (is_null($url) ||is_null($realm) ||is_null($method)) {
                throw new \Exception('url, realm and method should be provided in the Request object');
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
                    $matcher = new RouteMatch($route, null, null);
                    return $matcher;
                }
            }

            # search parameterized
            # from now on, the $match is instance of RouteMatch
            # and we will return it as-is
            $matched = null;
            $urlParser = new URLRouteParser($url);


            foreach ($parameterRoutes as $route) {
                if ($matched = $urlParser->matches($route, $this->predicateCompiler)) {
                    break;
                }
            }

            if (!$matched) {
                $matched = $this->routeCollection->getFourOhFour();
            }

            return $matched;
        }
    }