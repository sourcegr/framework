<?php


    namespace Sourcegr\Framework\Base\Interfaces;



    interface MiddlewareInterface
    {
        public function handle($app, callable $next);
    }