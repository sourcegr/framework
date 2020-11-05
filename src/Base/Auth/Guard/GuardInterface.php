<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\Base\Auth\Guard;


    use Sourcegr\Framework\Base\Auth\Engine\AuthUserProviderInterface;

    interface GuardInterface
    {

        public function loggedIn();

        public function guest();

        public function user();

        public function id();

        public function authenticate(array $credentials = []);

        public function setUser($user);
    }