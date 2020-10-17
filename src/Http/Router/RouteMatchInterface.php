<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\Http\Router;


    interface RouteMatchInterface
    {
        public function __construct(Route $route, URLRouteParser $parser, PredicateCompilerInterface $predicateCompiler);
        public function matches();
    }