<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\Http\Router;


    class RestfullRoute extends Route
    {
        public const ALLOWED_METHODS = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'];

        protected $allowedMethods = self::ALLOWED_METHODS;
        protected $controller;


        protected function getURL()
        {
            return $this->prefix . $this->url;
        }

        protected function checkMethods($methods)
        {
            if (is_string($methods)) {
                $methods = explode(',', str_replace(' ', '', $methods));
            }

            if (!is_array($methods)) {
                throw new \Exception('Restfull conrtollers allow method accepts an array or coma separated string');
            }

            return $methods;
        }

        public function allow($methods)
        {
            $this->allowedMethods = array_intersect(self::ALLOWED_METHODS, $this->checkMethods($methods));
        }

        public function exclude($methods)
        {
            $this->allowedMethods = array_diff($this->allowedMethods, $this->checkMethods($methods));
        }

        public function hasMethod($method)
        {
            return in_array($method, $this->allowedMethods);
        }


    }