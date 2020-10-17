<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\Http\Middleware;



    use Sourcegr\Framework\Http\Request\RequestInterface;

    class StartSessionMiddleware extends BaseMiddleware
    {
        public $request;

        public function __construct(RequestInterface $request)
        {
            $this->request = $request;
        }
    }