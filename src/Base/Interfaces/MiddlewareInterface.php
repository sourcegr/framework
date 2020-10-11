<?php


    namespace Sourcegr\Framework\Base\Interfaces;



    interface MiddlewareInterface
    {
        public function handle($request, callable $next);
    }