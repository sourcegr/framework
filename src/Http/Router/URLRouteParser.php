<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\Http\Router;


    use Sourcegr\Framework\Interfaces\Http\Router\URLRouteParserInterface;

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

        public function matches(Route $route)
        {
            $matcher = new RouteMatch($route, $this);
            return $matcher->matches();
        }
    }

