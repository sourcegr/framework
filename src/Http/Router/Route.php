<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\Http\Router;


    use Sourcegr\Framework\Base\Helpers\Arr;

    class Route implements RouteInterface
    {
        public const DEFAULT_REALM = 'WEB';
        public const IS_POSITIVE_NUMBER = '/^[1-9][0-9]*$/';

        public $callback;
        public $routeSegments = null;

        protected $url;
        protected $prefix;
        protected $realm = self::DEFAULT_REALM;
        protected $method;
        protected $hasWildcardParameter = false;
        protected $where;
        protected $predicates = [];
        protected $middlewares = [];

        protected function getURL()
        {
            return $this->prefix . $this->url;
        }


        public function __construct($realm, $method, $url, $callback, $predicate, $middlewares)
        {
            $this->realm = $realm ?? self::DEFAULT_REALM;
            $method = is_array($method) ? $method : [$method];
            $this->method = array_map('strtoupper', $method);
            $this->url = trim($url, '/');
            $this->callback = $callback;

            if ($predicate !== null) {
                $this->predicates[] = $predicate;
            }

            $this->$middlewares = $middlewares ?? [];
        }


        public function getRouteSegments() {
            if ($this->routeSegments) {
                return $this->routeSegments;
            }

            return $this->routeSegments = explode('/', $this->getCompiledParam('url'));
        }

        /**
         * @param string $param
         *
         * @return mixed|null
         */
        public function getCompiledParam(string $param)
        {
            return $param === 'url' ?
                $this->getURL() :
                $this->$param ?? null;
        }

        public function setPrefix(string $prefix): Route
        {
            $this->prefix = $prefix ? trim($prefix, "/") . '/' : '';
            return $this;
        }

        public function setMiddleware($middlewares): Route
        {
            if (!$middlewares) {
                return $this;
            }
            $middlewares = Arr::ensureArray($middlewares);
            $this->middlewares = Arr::merge($this->middlewares, $middlewares);
            return $this;
        }

        public function getMiddleware(): array
        {
            return $this->middlewares;
        }

        public function setRealm(string $realm): Route
        {
            $this->realm = $realm ?? self::DEFAULT_REALM;
            return $this;
        }

        public function setPredicate(callable $predicate): Route
        {
            if (!$predicate) {
                return $this;
            }
            $this->predicates[] = $predicate;
            return $this;
        }

        public function matchesAll(): Route
        {
            $this->hasWildcardParameter = true;
            return $this;
        }

        public function where($var, $regex = null): Route
        {
            if ($regex !== null) {
                $var = [$var => $regex];
            }

            $arrVar = Arr::ensureArray($var);

            $this->where = function ($params) use ($arrVar) {
                $matches = true;

                foreach ($arrVar as $variable => $regexp) {
                    $paramValue = $params[$variable] ?? null;

                    if ($params === null) {
                        return false;
                    }
                    $matches = $matches && ((bool)preg_match($regexp, $paramValue));
                }
                return $matches;
            };

            return $this;
        }
    }