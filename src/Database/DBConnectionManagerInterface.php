<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\Database;



    use Sourcegr\Framework\Database\PDOConnection\PDOConnection;
    use PDO;

    interface DBConnectionManagerInterface
    {

        public function getConnection(string $name): ?PDO;

        public function create(string $name, string $driver, array $config) : PDOConnection;
    }