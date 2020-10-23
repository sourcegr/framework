<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\Http\Request;


    use Sourcegr\Framework\Base\Encryptor\EncryptorInterface;

    interface CookieInterface
    {
        public function setEncryptorEngine(EncryptorInterface $encryptor = null);

        public function get($key = null, $default = null);

        public function sendQueuedCookies($cookies);
    }