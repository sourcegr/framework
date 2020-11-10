<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\Http\Router;

    use Sourcegr\Framework\Base\Helpers\Arr;

    class RestfullRoute
    {
        public const ALLOWED_METHODS = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'];

        protected $allowedMethods = self::ALLOWED_METHODS;
        protected $controller;
        protected $url;
        protected $varName;
        protected $extra = [];
        protected $relations = [];


        public function __construct($url, $controller)
        {
            $this->url = $url;
            $this->controller = $controller;

            $this->varName = Arr::last(explode('/', $url));
        }

        protected function getMethodMap($m)
        {
            return [
                    'GET' => '',
                    'POST' => '',
                    'PUT' => '/#' . $this->varName,
                    'PATCH' => '/#' . $this->varName,
                    'DELETE' => '/#' . $this->varName
                ][$m] ?? null;
        }

        protected function checkMethods($methods)
        {
            if (is_string($methods)) {
                $methods = explode(',', strtoupper(str_replace(' ', '', $methods)));
            }

            if (!is_array($methods)) {
                throw new \Exception('Restfull controllers allow method accepts an array or coma separated string');
            }

            return $methods;
        }


        public function setController($controller)
        {
            $this->controller = $controller;
            return $this;
        }

        public function setVarName($varName)
        {
            $this->varName = $varName;
            return $this;
        }

        public function allow($methods)
        {
            $this->allowedMethods = array_intersect(self::ALLOWED_METHODS, $this->checkMethods($methods));
            return $this;
        }

        public function exclude($methods)
        {
            $this->allowedMethods = array_diff($this->allowedMethods, $this->checkMethods($methods));
            return $this;
        }

        public function add(string $route, string $method, bool $useParameter = true, string $controllerMethod = null)
        {
            if (!$controllerMethod) $controllerMethod = $route;
            $this->extra[] = [$route, $method, $controllerMethod, $useParameter];
            return $this;
        }

//        public function hasMethod($method)
//        {
//            return in_array($method, $this->allowedMethods);
//        }

        public function addRelation($relation, $controller = null)
        {
            if (!$controller) {
                $controller = $this->controller;
            }
            $relation = trim($relation, '/');
            $r = new static('', $controller);
            $r->allow('get,post'); // get is the list, post is the creation
            $this->relations[$relation] = $r;

            return $r;
        }

        public function getCompiledRoutes()
        {
            $r = [];

            foreach ($this->allowedMethods as $method) {
                $r[] = [
                    [$method],
                    $this->url . $this->getMethodMap($method),
                    $this->controller,
                    $method . $this->url
                ];
            }


            foreach ($this->extra as [$route, $method, $controllerMethod, $useParameter]) {
                $methodUC = ucfirst($controllerMethod);
                $r[] = [
                    [$method],
                    $this->url . ($useParameter ? '/#' . $this->varName : '') . '/' . $route,
                    $this->controller,
                    $method . $this->url . $methodUC
                ];
            }


            foreach ($this->relations as $relationName => $relation) {
                $relationUC = ucfirst($relationName);
                $urlUC = ucfirst($this->url);
                foreach ($relation->allowedMethods as $method) {
                    $r[] = [
                        [$method],
                        $this->url . '/#' . $this->varName . '/' . $relationName . $relation->getMethodMap($method),
                        $this->controller,
                        $method . $urlUC . $relationUC
                    ];
                }



                foreach (['GET', 'PATCH', 'DELETE'] as $method) {
                    $r[] = [
                        [$method],
                        $relationName . '/#' . $relation->varName,
                        $relation->controller,
                        $method  . $relationUC
                    ];
                }

                foreach ($relation->extra as [$route, $method, $controllerMethod, $useParameter]) {
                    $routeUC = ucfirst($route);
                    $r[] = [
                        [$method],
                        $relationName . '/#' . $relation->varName .'/' . $route,
                        $relation->controller,
                        $method . $relationUC . $routeUC
                    ];
                }
            }
//            $rr = [];foreach ($r as $item) {$rr[] = $item[1] .' - '. $item[3];} dd($rr);
            return $r;
        }
    }

