<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\Base\Auth\Engine;


    abstract class BaseAuthUserProvider implements AuthUserProviderInterface
    {
        protected $idField;
        protected $tokenField;
        protected $loginField;
        protected $passwordField;

        public $user;


        public function __construct($config)
        {
            $this->idField = $config['id_field'];
            $this->tokenField = $config['token_field'];
            $this->loginField = $config['login_field'];
            $this->passwordField = $config['password_field'];
            $this->user = null;
        }


        public function getIdField()
        {
            return $this->idField;
        }

        public function isLoggedIn(): bool
        {
            return $this->user ? true : false;
        }

        public function getUser()
        {
            return $this->user ?: null;
        }


        public function setIdField($idField): void
        {
            $this->idField = $idField;
        }


        public function getTokenField()
        {
            return $this->tokenField;
        }


        public function setTokenField($tokenField): void
        {
            $this->tokenField = $tokenField;
        }


        public function getLoginField()
        {
            return $this->loginField;
        }


        public function setLoginField($loginField): void
        {
            $this->loginField = $loginField;
        }


        public function getPasswordField()
        {
            return $this->passwordField;
        }

        public function setPasswordField($passwordField): void
        {
            $this->passwordField = $passwordField;
        }

    }