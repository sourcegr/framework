<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\Http\Router;


    interface PredicateCompilerInterface
    {
        public function runPredicate($callback, RouteMatchInterface $routeMatch);
    }