<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\Http\Middleware;


    use Sourcegr\Framework\App\AppInterface;
    use Sourcegr\Framework\Http\Response\ResponseInterface;

    class BaseMiddleware
    {
        protected $app = null;

        public function __construct(AppInterface $app)
        {
            $this->app = $app;
        }
    }