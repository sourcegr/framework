<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\Http\Router;


    class PredicateCompiler implements PredicateCompilerInterface
    {

        public function runPredicate($callback, RouteMatchInterface $routeMatch)
        {
            return $callback($routeMatch);
        }
    }