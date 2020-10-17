<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\App;


    use Sourcegr\Framework\Http\Request\RequestInterface;

    interface KernelInterface
    {
        public function handle(RequestInterface $request);
    }