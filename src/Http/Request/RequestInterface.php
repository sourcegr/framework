<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\Http\Request;


    interface RequestInterface
    {
        public function setRealm(string $realm): RequestInterface;

        public function __construct(
            string $url = '',
            array $get = [],
            array $post = [],
            array $cookie = [],
            array $files = [],
            array $server = []
        );

        public function expectsJson(): bool;

        public function getMethod(): string;

        public function getHeader(string $header): ?string;

        public function get(string $key, string $type = null): ?string;

        public function filesArray(): array;

        public function getSession();
    }