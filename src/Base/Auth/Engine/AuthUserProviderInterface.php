<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\Base\Auth\Engine;


    interface AuthUserProviderInterface
    {
        public function authenticate(array $credentials);
        public function isLoggedIn():bool;
        public function getUserById($id);
    }