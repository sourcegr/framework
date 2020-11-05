<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\Base\Auth\Engine;


    interface AuthUserProviderInterface
    {
        public function setHasher($hasher);

        public function checkUser(array $credentials);
        public function createUserToken($user);

        public function isLoggedIn(): bool;
        public function isGuest(): bool;

        public function getUserById($id);
        public function getUserByToken($token);

        public function getUser();
        public function getLoggedUserId($user);

        public function getIdField();
        public function setIdField($idField): void;

        public function getTokenField();
        public function setTokenField($tokenField): void;

        public function getLoginField();
        public function setLoginField($loginField): void;

        public function getPasswordField();
        public function setPasswordField($passwordField): void;

    }