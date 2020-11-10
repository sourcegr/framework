<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\Base\Auth\Guard;


    use Sourcegr\Framework\Http\Request\RequestInterface;

    class TokenGuard implements GuardInterface
    {
        use GuardTrait;



        protected $request = null;
        protected $allowPost;
        protected $allowGet;
        protected $tokenName;

        protected $authProvider = null;
        protected $user;


        public function __construct(RequestInterface $request, $guardConfig = [])
        {
            $this->allowPost = $guardConfig['allow_POST'] ?? false;
            $this->allowGet = $guardConfig['allow_GET'] ?? false;
            $this->tokenName = $guardConfig['token_name'] ?? null;
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

            $tokenName = $this->tokenName ?? $this->authProvider->getTokenField();

            $token = $this->request->getBearerToken();


            if (!$token && $this->allowPost) {
                $token = $this->request->get($tokenName, 'POST');
            }

            if (!$token && $this->allowGet) {
                $token = $this->request->get($tokenName, 'GET');
            }

            if ($token) {
                $user = $this->authProvider->getUserByToken($token);
            }
            return $this->user = $user;
        }

        public function authenticate(array $credentials = [])
        {
            // TODO: Implement authenticate() method.
        }
    }