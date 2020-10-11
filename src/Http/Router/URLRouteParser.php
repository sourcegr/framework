<?php


    namespace Sourcegr\Framework\Http\Router;


    class URLRouteParser
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

        public function matches(Route $route)
        {
            $matcher = new RouteMatch($route, $this);
            return $matcher->matches();
        }
    }

