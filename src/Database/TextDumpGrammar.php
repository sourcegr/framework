<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\Database;


    use PDO;



    class TextDumpGrammar implements GrammarInterface
    {

        protected $defaultFetchMode = PDO::FETCH_ASSOC;
        protected $connection;

        /**
         * @return PDO
         */
        public function getConnection()
        {
            return $this->connection;
        }


        public function __construct(PDO $connection = null)
        {
            $this->connection = $connection;
        }


        /**
         * @param int $count
         *
         * @return string
         */
        public function getPlaceholder($count = 0)
        {
            $PLACEHOLDER = '?';
            return $count ? implode(',', array_fill(0, $count, $PLACEHOLDER)) : $PLACEHOLDER;
        }


        /**
         * @param null $count
         * @param null $startAt
         *
         * @return string|null
         */
        public function createLimit($count = null, $startAt = null)
        {
            if ($count && $startAt) {
                return "LIMIT $count OFFSET $startAt";
            }

            if ($count) {
                return "LIMIT $count";
            }

            return null;
        }


        public function select($sqlString, $sqlParams, $mode = null)
        {
            return [$sqlString, $sqlParams, 'SELECT'];
        }


        public function insert($sqlString, $sqlParams)
        {
            return [$sqlString, $sqlParams, 'INSERT'];
        }

        public function update($sqlString, $sqlParams)
        {
            return [$sqlString, $sqlParams, 'UPDATE'];
        }

        public function delete($sqlString, $sqlParams)
        {
            return [$sqlString, $sqlParams, 'DELETE'];
        }
    }