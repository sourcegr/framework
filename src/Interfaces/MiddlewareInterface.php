<?php


    namespace Sourcegr\Framework\Interfaces;



    interface MiddlewareInterface
    {
        public function handle($app, callable $next);
    }