<?php

    declare(strict_types=1);

    namespace Sourcegr\Framework\Database\PDOConnection;

    use PDO;

    class PDOConnection
    {
        /**
         * @var PDO
         */
        public $connection;

        public function __construct(string $connectionString, $user, $password, $PDOParams = null)
        {
            $this->connection = new \PDO($connectionString, $user, $password, $PDOParams);
        }
    }