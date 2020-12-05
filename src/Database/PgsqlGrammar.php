<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\Database;


    use PDO;
    use Sourcegr\Framework\Database\QueryBuilder\Exceptions\SelectErrorException;
    use Sourcegr\Framework\Database\QueryBuilder\Exceptions\InsertErrorException;
    use Sourcegr\Framework\Database\QueryBuilder\Exceptions\UpdateErrorException;
    use Sourcegr\Framework\Database\QueryBuilder\Exceptions\DeleteErrorException;


    class PgsqlGrammar implements GrammarInterface
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


        public function __construct(PDO $connection)
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


        /**
         * @param      $sqlString
         * @param      $sqlParams
         * @param null $mode
         *
         * @return array
         * @throws SelectErrorException
         */
        public function select($sqlString, $sqlParams, $mode = null)
        {
            $mode = $mode ?? $this->defaultFetchMode;
            $st = $this->connection->prepare($sqlString);

            $res = $st->execute($sqlParams);

            if ($res === false) {
                $info = $st->errorInfo();
                throw new SelectErrorException($info[0] . ': ' . $info[2] . ' (' . $info[1] . ')');
            }

            return $st->fetchAll($mode);
        }

        /**
         * @param $sqlString
         * @param $sqlParams
         *
         * @return string
         * @throws InsertErrorException
         */
        public function insert($sqlString, $sqlParams)
        {
            $st = $this->connection->prepare($sqlString);
            $res = $st->execute($sqlParams);

            if ($res === false) {
                $info = $st->errorInfo();
                throw new InsertErrorException($info[0] . ': ' . $info[2] . ' (' . $info[1] . ')');
            }

            return $this->connection->lastInsertId();
        }

        /**
         * @param $sqlString
         * @param $sqlParams
         *
         * @return int
         * @throws UpdateErrorException
         */
        public function update($sqlString, $sqlParams)
        {
            $st = $this->connection->prepare($sqlString);
            $res = $st->execute($sqlParams);

            if ($res === false) {
                $info = $st->errorInfo();
                throw new UpdateErrorException($info[0] . ': ' . $info[2] . ' (' . $info[1] . ')');
            }

            return $st->rowCount();
        }


        /**
         * @param $sqlString
         * @param $sqlParams
         *
         * @return int
         * @throws DeleteErrorException
         */
        public function delete($sqlString, $sqlParams)
        {
            $st = $this->connection->prepare($sqlString);
            $res = $st->execute($sqlParams);

            if ($res === false) {
                $info = $st->errorInfo();
                throw new DeleteErrorException($info[0] . ': ' . $info[2] . ' (' . $info[1] . ')');
            }

            return $st->rowCount();
        }
    }