<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\Http\Response;


    interface ResponseInterface
    {
        public function init();

        public function header($name, $value);

        public function addHeaders($headers);

        public function json($data);

        public function sendFile($file);

        public function downloadFile($file);


        public function setCookieBag($cookieBag);

        public function setSessionCookieParams($params);

        public function setSessionCookie($id);

        public function setCookie(
            $key,
            $value,
            $expires = null,
            $path = null,
            $domain = null,
            $secure = false,
            $httpOnly = false
        );

        public function deleteCookie(string $key);

        public function sendCookies();
    }