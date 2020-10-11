<?php


    namespace Sourcegr\Framework\Http\Router;


    use Sourcegr\Framework\Base\Helpers\Arr;

    class Route
    {
        public const IS_NUMBER = '/^[0-9][0-9]$/';

        protected $url;
        protected $realm;
        protected $method;
        protected $hasWildcardParameter = false;
        protected $where;
        protected $predicates = [];
        protected $callback;
        protected $middlewares = [];

        public function __construct($method, $url, $callback, $predicate, $middlewares)
        {
            $this->method = $method;
            $this->url = trim($url, "/");
            $this->callback = $callback;

            if ($predicate !== null) {
                $this->predicates[] = $predicate;
            }

            $this->$middlewares = $middlewares ?? [];
        }

        public function getCompiledParam(string $param) {
            return $this->$param ?? null;
        }

        public function setPrefix(string $prefix): Route
        {
            $prefix = $prefix ? trim($prefix, "/") . '/' : '';
            $this->url = $prefix . $this->url;
            return $this;
        }

        public function setMiddleware($middlewares): Route
        {
            $middlewares = Arr::ensureArray($middlewares);
            $this->middlewares = Arr::merge($this->middlewares, $middlewares);
            return $this;
        }

        public function setRealm(string $realm): Route
        {
            $this->realm = $realm;
            return $this;
        }

        public function setPredicate(callable $predicate): Route
        {
            $this->predicates[] = $predicate;
            return $this;
        }

        public function matchesAll() {
            $this->hasWildcardParameter = true;
        }

        public function where($var, $regex = null): Route
        {
            if ($regex !== null) {
                $var = [$var => $regex];
            }

            $arrVar = Arr::ensureArray($var);

            $this->where = function ($params) use ($arrVar) {
                $matches = true;

//                foreach ($params as $variable => $value) {
//                    $matches = $matches && ((bool)preg_match($arrVar[$variable], $value));
//                }
                foreach ($arrVar as $variable => $regexp) {
                    $paramValue = $params[$variable] ?? null;
                    if (!$params) {
                        return false;
                    }
                    $matches = $matches && ((bool)preg_match($regexp, $paramValue));
                }
                return $matches;
            };

            return $this;
        }
    }