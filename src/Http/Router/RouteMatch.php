<?php


    namespace Sourcegr\Framework\Http\Router;


    use Sourcegr\Framework\Base\Helpers\Arr;

    class RouteMatch
    {
        public $varsMap = [];
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

            if ($routeHasOptional && !$routeHasWildcard && $urlSegmentsLength <= $routeSegmentsLength-2 ) {
                # we have an optional, but we dont match to the end.
                # So, the length should be either the same or one lower
                return null;
            }

            foreach ($routeSegments as $index => $segment) {
                # we first check to see if there is a required variable
                if (strncmp($segment, '#', 1) === 0) {
                    $segment = substr($segment, 1);

                    $this->varsMap[$segment] = $urlSegments[$index];
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
                        $this->varsMap[$segment] = implode('/', $rest);
                        break;
                    } else {
                        if ($urlSegmentsLength > $routeSegmentsLength) {
                            return null;
                        }

                        if ($urlSegmentsLength < $routeSegmentsLength) {
                            $this->varsMap[$segment] = null;
                        } else {
                            $this->checkMap[$index] = $urlSegments[$index];
                            $this->varsMap[$segment] = $urlSegments[$index];
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
                    $result = $where($this->varsMap, $this) && $result;
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