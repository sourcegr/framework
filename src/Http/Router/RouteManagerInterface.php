<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\Http\Router;


    use Sourcegr\Framework\Http\Request\RequestInterface;

    interface RouteManagerInterface
    {
        public function __construct(RequestInterface $request, PredicateCompilerInterface $predicateCompiler);

        public function matchRoute(callable $callback);
    }