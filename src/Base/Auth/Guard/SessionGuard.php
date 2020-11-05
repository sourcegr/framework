<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\Base\Auth\Guard;


    use Sourcegr\Framework\Http\Request\RequestInterface;
    use Sourcegr\Framework\Http\Session\SessionBag;
    use Sourcegr\Framework\Http\Session\SessionInterface;

    class SessionGuard implements GuardInterface
    {
        use GuardTrait;


        public $authProvider = null;
        public $session = null;
        public $user = null;


        public function __construct(SessionInterface $session)
        {
            $this->session = $session;
        }

        public function authProvider()
        {
            return $this->authProvider;
        }

        public function authenticate(array $credentials = [])
        {
            $result = $this->authProvider->checkUser($credentials);

            $this->user = $result;

            if ($result) {
                $this->session->setUserId($this->authProvider->getLoggedUserId($this->user));
            }
            return $this->user;
        }

        public function user()
        {
            if (!$this->authProvider) {
                throw new \Exception('TokenGuard: No authProvider');
            }

            if (!is_null($this->user)) {
                return $this->user;
            }

            $user = null;
            // try to get by id field in the Session

            $id = $this->session->getUserId();

            if ($id) {
                $user = $this->authProvider->getUserById($id);
            }

            return $this->user = $user;
        }

        public function logout() {
            $this->session->setUserId(null);
            $this->session->regenerateToken();
        }
    }