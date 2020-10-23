<?php

    declare(strict_types=1);

    namespace Sourcegr\Framework\Database\PDOConnection;

    use PDO;
    use Sourcegr\Framework\Database\GrammarInterface;

    class PDOConnection
    {
        /**
         * @var PDO
         */
        public $connection;
        public $grammar;


        public function __construct(string $connectionString, $user, $password, $PDOParams = null)
        {
            $this->connection = new \PDO($connectionString, $user, $password, $PDOParams);
        }

        public function setGrammar(GrammarInterface $grammar)
        {
            $this->grammar = $grammar;
        }
    }