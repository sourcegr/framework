<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\Http\Middleware;



    abstract class BaseMiddleware
    {
        protected $app = null;
        protected $name = null;

        public function setName(string $name) {
            $this->name = $name;
        }

        public function setRequestData($name, $value) {
            $this->app->getRequest()->data->$name = $value;
        }
    }