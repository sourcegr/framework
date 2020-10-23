<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\Base\Encryptor;


    interface EncryptorInterface
    {

        public function addPrefix($to);

        public function removePrefix($from);

        public function __construct($key, $cipher = 'AES-128-CBC');

        public function encrypt($value, $serialize = true);

        public function encryptString($value);

        public function decrypt($payload, $unserialize = true);

        public function decryptString($payload);

        public function getKey();
    }