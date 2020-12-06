<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\Database;


    use PDO;

    interface GrammarInterface
    {
        public function getConnection();

        public function __construct(PDO $connection);

        public function getPlaceholder($count = 0);

        public function createLimit($count = null, $startAt = null);

        public function select($sqlString, $sqlParams, $mode = null);

        public function insert($sqlString, $sqlParams, $returning = null);

        public function update($sqlString, $sqlParams, $returning = null);

        public function delete($sqlString, $sqlParams, $returning = null);
    }