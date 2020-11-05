<?php


    namespace Sourcegr\Framework\Base\Auth\Guard;


    use Sourcegr\Framework\Base\Auth\Engine\AuthUserProviderInterface;

    Trait GuardTrait
    {
        public function setProvider(AuthUserProviderInterface $authProvider)
        {
            $this->authProvider = $authProvider;
        }

        public function getProvider()
        {
            return $this->authProvider;
        }

        public function loggedIn()
        {
            return is_null($this->user);
        }

        public function guest()
        {
            return !$this->loggedIn();
        }

        public function id()
        {
            // TODO: Implement id() method.
        }

        public function setUser($user)
        {
            $this->user = $user;
            return $this;
        }
    }