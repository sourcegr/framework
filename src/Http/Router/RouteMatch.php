<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\Http\Router;


    use Sourcegr\Framework\Base\Helpers\Arr;
    use Sourcegr\Framework\Interfaces\Http\Router\RouteMatchInterface;

    class RouteMatch implements RouteMatchInterface
    {
        public $vars = [];
        public $route;

        protected $parser;

        protected $checkMap = [];

        /**
         * UrlRouteMatcher constructor.
         *
         * @param Route          $route
         * @param URLRouteParser $parser
         */
        public function __construct(Route $route, URLRouteParser $parser = null)
        {
            $this->route = $route;
            $this->parser = $parser;
        }

        public function matches()
        {
            # get number of optional params
            $numFound = substr_count($this->route->getCompiledParam('url'), '?');

            if ($numFound > 1) {
                # we cannot have more than one optional, though...
                throw new \Exception('Route should not have more than one optional parameter');
            }

            $routeHasOptional = $numFound > 0;
            $routeHasWildcard = $routeHasOptional && $this->route->getCompiledParam('hasWildcardParameter');

            $routeSegments = explode('/', $this->route->getCompiledParam('url'));
            $routeSegmentsLength = count($routeSegments);

            $urlSegments = $this->parser->urlSegments;
            $urlSegmentsLength = $this->parser->urlSegmentsLength;

            if (!$routeHasOptional && $routeSegmentsLength !== $urlSegmentsLength){
                // No optional vars, so segment lengths should be the same
                // on both the URL and the Route
                return null;
            }

            $countDiff = $routeSegmentsLength - $urlSegmentsLength;

            if ($routeHasOptional){
                #fail on few
                if ($countDiff > 1) {
                    return null;
                }

                #fail on too many if no wildcard
                if (!$routeHasWildcard && $countDiff < 0) {
                    return null;
                }
            }

            foreach ($routeSegments as $index => $segment) {
                # we first check to see if there is a required variable
                if (strncmp($segment, '#', 1) === 0) {
                    $segment = substr($segment, 1);

                    $this->vars[$segment] = $urlSegments[$index];
                    $this->checkMap[$index] = $urlSegments[$index];
                    continue;
                }

                #if not, we are looking for an optional variable
                if (strncmp($segment, '?', 1) === 0) {
                    $segment = substr($segment, 1);

                    # case 1: this is a wildcard
                    if ($routeHasWildcard) {
                        # match all forward
                        $rest = array_slice($urlSegments, $index);
                        $this->checkMap = Arr::merge($this->checkMap, $rest);
                        $this->vars[$segment] = implode('/', $rest);
                        break;
                    } else {
                        if ($urlSegmentsLength < $routeSegmentsLength) {
                            $this->vars[$segment] = null;
                        } else {
                            $this->checkMap[$index] = $urlSegments[$index];
                            $this->vars[$segment] = $urlSegments[$index];
                        }


                        continue;

                        # contact/?rest
                        #  = contact/
                        #  = contact/create
//                        if ($routeSegmentsLength  $urlSegmentsLength)
                    }
                }

                # otherwise, no variable exists and we set the
                $this->checkMap[$index] = $segment;
            }

//            var_dump([$this->checkMap , $urlSegments]);
            // if the resulting array is the same, we have a match
            if ($this->checkMap === $urlSegments) {
                $result = true;

                // then check if there is a where
                /** @var callable $where */
                if ($where = $this->route->getCompiledParam('where')) {
                    $result = $where($this->vars, $this) && $result;
                }

                // if there are any predicates, check if they are met
                $predicates = $this->route->getCompiledParam('predicates');
                if (count($predicates)) {
                    foreach ($predicates as $predicate) {
                        $result = $result && $predicate($this);
                    }
                }

                if ($result) {
                    return $this;
                }
            }

            // no match found
            return null;
        }
    }