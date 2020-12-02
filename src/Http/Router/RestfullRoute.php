<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\Http\Router;

    use Sourcegr\Framework\Base\Helpers\Arr;
    use Sourcegr\Framework\Base\Helpers\Str;

    class RestfullRoute
    {
        public const ALLOWED_METHODS = ['GET', 'POST'];
        public const ALLOWED_RELATION_METHODS = ['GET', 'PUT', 'PATCH', 'DELETE'];

        protected $allowedMethods = self::ALLOWED_METHODS;
        protected $allowedRelationMethods = self::ALLOWED_RELATION_METHODS;
        protected $controller;
        protected $url;
        protected $varName;
        protected $extra = [];
        protected $relations = [];
        protected $extraRoutes = [];
        protected $allowRelationGET = false;
        protected $allowRelationPOST = false;



        protected function getMethodMap($m)
        {
            return [
                    'POST' => '',
                    'GET' => '', ///#' . $this->varName,
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


        public function getCompiledRoutes()
        {
            $r = [];
            $urlUC = Str::toCamelCase($this->url, true);
            foreach ($this->allowedMethods as $method) {
                $r[] = [
                    [$method],
                    $this->url,
                    $this->controller,
                    $method . $urlUC
                ];
            }

            foreach ($this->allowedRelationMethods as $method) {
                $r[] = [
                    [$method],
                    $this->url. '/#' . $this->varName,
                    $this->controller,
                    $method . $urlUC . 'Item'
                ];
            }


            foreach ($this->extra as [$route, $method, $controllerMethod, $useParameter]) {
                $methodUC = Str::toCamelCase($controllerMethod, true);
                $r[] = [
                    [$method],
                    $this->url . ($useParameter ? '/#' . $this->varName : '') . '/' . $route,
                    $this->controller,
                    $method . $urlUC . ($useParameter?'Item':'') . $methodUC
                ];
            }


            foreach ($this->relations as $relationName => $relation) {
                $relationUC = Str::toCamelCase($relationName, true);

                foreach ($relation->allowedMethods as $method) {
                    $r[] = [
                        [$method],
                        $this->url . '/#' . $this->varName . '/' . $relationName . $relation->getMethodMap($method),
                        $this->controller,
                        $method . $urlUC . 'Item' . $relationUC
                    ];
                }
                foreach ($relation->extra as [$route, $method, $controllerMethod, $useParameter]) {
                    $routeUC = Str::toCamelCase($route, true);
                    $r[] = [
                        [$method],
                        $relationName . '/#' . $relation->varName . '/' . $route,
                        $relation->controller,
                        $method . $relationUC . ($useParameter ? 'Item' : '') . $routeUC
                    ];
                }
                if ($relation->allowRelationGET) {
                    $r[] = [
                        ['GET'],
                        $relationName,
                        $relation->controller,
                        'GET'.$relationUC
                    ];
                }
                if ($relation->allowRelationPOST) {
                        $r[] = [
                        ['POST'],
                        $relationName,
                        $relation->controller,
                        'POST'.$relationUC
                    ];
                }
                foreach ($relation->allowedRelationMethods as $method) {
                    $r[] = [
                        [$method],
                        $relationName . '/#' . $relation->varName,
                        $relation->controller,
                        $method . $relationUC. 'Item'
                    ];
                }
            }
//            $rr = [];foreach ($r as $item) {$rr[] = $item;}dd($rr);
//            $rr = [];foreach ($r as $item) {$rr[] = $item[1] . ' - ' . $item[2].'@'.$item[3];}dd($rr);
            return array_merge($this->extraRoutes, $r);
        }

        public function __construct($url, $controller)
        {
            $this->url = $url;
            $this->controller = $controller;

            $this->varName = Arr::last(explode('/', $url));
        }

        public function rest($url, $controller, $callback)
        {
            $rr = new static($url, $controller);
            $callback($rr);

//            dd($rr->getCompiledRoutes());
            foreach ($rr->getCompiledRoutes() as &$route) {
                $route[1] = $this->url . '/' . $route[1];
                $this->extraRoutes[] = $route;
            }
            return $this;
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

        public function excludeMethod($methods)
        {
            $this->allowedMethods = array_diff($this->allowedMethods, $this->checkMethods($methods));
            return $this;
        }

        public function excludeRelationMethod($methods)
        {
            $this->allowedRelationMethods = array_diff($this->allowedRelationMethods, $this->checkMethods($methods));
            return $this;
        }


        public function allowGET() {
            $this->allowRelationGET = true;
            return $this;
        }
        public function allowPOST() {
            $this->allowRelationPOST = true;
            return $this;
        }
        public function add(string $route, string $method, bool $useParameter = true, string $controllerMethod = null)
        {
            if (!$controllerMethod) {
                $controllerMethod = $route;
            }
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

        public function excludeRelation($methods)
        {
            $this->allowedRelationMethods = array_diff($this->allowedRelationMethods, $this->checkMethods($methods));
            return $this;
        }
    }

