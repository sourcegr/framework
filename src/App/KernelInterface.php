<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\App;


    use Sourcegr\Framework\Http\Router\RouteMatchInterface;

    interface KernelInterface
    {
        public function handleRoute(RouteMatchInterface $route);
    }