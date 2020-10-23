<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\Base\Auth\Guard;


    use Sourcegr\Framework\Http\Request\RequestInterface;

    class SessionGuard implements GuardInterface
    {
        use GuardTrait;


        public $authProvider = null;
        public $request = null;
        public $user = null;



        public function __construct(RequestInterface $request)
        {
            $this->request = $request;
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
            $id = $this->request->session->getUserIdField();

            if ($id) {
                $user = $this->authProvider->getUserById($id);
            }

            return $this->$user = $user;
        }
    }