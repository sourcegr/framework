<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\Http\Router;


    class URLRouteParser implements URLRouteParserInterface
    {
        public $url;
        public $urlSegments;
        public $urlSegmentsLength;

        public function __construct($url)
        {
            $this->url = $url;
            $this->urlSegments = explode('/', trim($url, "/"));
            $this->urlSegmentsLength = count($this->urlSegments);
        }

        public function matches(Route $route, PredicateCompilerInterface $predicateCompiler)
        {
            $matcher = new RouteMatch($route, $this, $predicateCompiler);
            return $matcher->matches();
        }
    }

