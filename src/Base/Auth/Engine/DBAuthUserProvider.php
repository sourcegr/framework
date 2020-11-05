<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\Base\Auth\Engine;


    use Sourcegr\Framework\Base\Auth\Guard\GuardInterface;
    use Sourcegr\Framework\Base\Hashing\Engine\HasherInterface;
    use Sourcegr\Framework\Database\QueryBuilder\DBInterface;
    use Sourcegr\Framework\Http\Session\SessionBag;

    class DBAuthUserProvider extends BaseAuthUserProvider implements AuthUserProviderInterface
    {
        protected $table;

        /**
         * @var DBInterface|null $queryBuilder
         */
        protected $queryBuilder;
        /**
         * @var HasherInterface $hasher |null
         */
        protected $hasher;

        public function __construct($config, $hasher = null, $queryBuilder = null)
        {
            parent::__construct($config);
            $this->queryBuilder = $queryBuilder;
            $this->hasher = $hasher;
            $this->table = $config['table'];
        }


        protected function QB()
        {
            return $this->queryBuilder->Table($this->table);
        }

        protected function getUserResult($result)
        {
            if (!count($result)) {
                return null;
            }

            return $result[0];
        }

        public function setQueryBuilder($queryBuilder)
        {
            $this->queryBuilder = $queryBuilder;
        }


        public function createUserToken($user) {
            // todo create the token to be sent to the user. Could this be JWT?
        }

        public function setHasher($hasher)
        {
            $this->hasher = $hasher;
        }

        public function checkUser(array $credentials)
        {
            $dbField = $credentials[$this->loginField] ?? 'email';
            $password = $credentials[$this->passwordField] ?? 'password';

            $res = $this->QB()->where($this->loginField, $dbField)->select();

            if (!count($res)) {
                return false;
            }

            $user = $res[0];
            $encryptedPassword = $user[$this->passwordField];


            $success = $this->hasher->checkHash($password, $encryptedPassword);

            return $success ? $user : false;
        }

        public function getUserById($id)
        {
            $id = (int)$id;
            return $this->getUserResult($this->QB()->where($this->idField, $id)->select());
        }

        public function getLoggedUserId($user) {
            return $user[$this->idField];
        }

        public function getUserByToken($token)
        {
            return $this->getUserResult($this->QB()->where($this->tokenField, $token)->select());
        }
    }