<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\App;


    use Sourcegr\Framework\Http\Request\RequestInterface;

    interface AppInterface
    {
        public function getRequest(): RequestInterface;

        public function isDownForMaintenance();

        public function loadAppConfig($key);

        public function loadConfig($file, $key = null);

        public function loadConfigFile($file, $key = null);

        public function execServiceProviders();

        public function bootServiceProviders();

        public function execMiddleware();
    }