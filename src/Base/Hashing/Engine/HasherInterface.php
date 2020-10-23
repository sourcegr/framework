<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\Base\Hashing\Engine;


    interface HasherInterface
    {
        public function __construct($config);

        public function createHash(string $value, array $options = []): string;

        public function checkHash(string $value, string $hashed): bool;
    }