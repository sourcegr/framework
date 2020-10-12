<?php


    namespace Sourcegr\Tests\Http\Router;


    use Sourcegr\Framework\Http\Router\RouteCollection;

    class DebugRouteCollection extends RouteCollection
    {
        public function getProp($prop)
        {
            return $this->$prop;
        }
    }
