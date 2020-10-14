<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\Base\Interfaces;





    interface KernelInterface
    {
        public function init();
        public function handle(RequestInterface $request) :ResponseInterface;
        public function terminate(RequestInterface $request, ResponseInterface $response) :void;
        public function app(): AppInterface;
    }