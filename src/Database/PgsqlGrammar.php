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

        public function __construct(PDO $connection)
        {
            $this->connection = $connection;
        }

        protected function fixBooleanFalse($params) {
            foreach ($params as $key => $param) {
                if ($param === false) {
                    $params[$key] = 'f';
                }
            }
            return $params;
        }

        /**
         * @return PDO
         */
        public function getConnection()
        {
            return $this->connection;
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
        public function select($sqlString, $sqlParams, $returning=null)
        {
            try {
                $mode = $mode ?? $this->defaultFetchMode;
                $st = $this->connection->prepare($sqlString);

                $res = $st->execute($this->fixBooleanFalse($sqlParams));

                if ($res === false) {
                    $info = $st->errorInfo();
                    throw new SelectErrorException($info[0] . ': ' . $info[2] . ' (' . $info[1] . ')');
                }
                return $st->fetchAll($mode);
            } catch (\Exception $e) {
                throw new SelectErrorException('sql error: '. $e->getMessage(). ' ## ' . $sqlString . '##' . json_encode($sqlParams, JSON_UNESCAPED_UNICODE));
            }

        }

        /**
         * @param      $sqlString
         * @param      $sqlParams
         * @param null $returning
         *
         * @return mixed
         * @throws InsertErrorException
         */
        public function insert($sqlString, $sqlParams, $returning = null)
        {
            try {
                if ($returning !== null) {
                    $sqlString .= " RETURNING " . implode(',', ($returning));
                }
                $st = $this->connection->prepare($sqlString);
                $res = $st->execute($this->fixBooleanFalse($sqlParams));

                if ($res === false) {
                    $info = $st->errorInfo();
                    throw new InsertErrorException($info[0] . ': ' . $info[2] . ' (' . $info[1] . ')');
                }

                if ($returning !== null) {
                    $vals = $st->fetchAll();
                    return $vals[0];
                }

                return $this->connection->lastInsertId();
            } catch (\PDOException $e) {
                if ($e->getCode() == "55000") {
                    // probable "Object not in prerequisite state:" error
                    return true;
                }
                throw new InsertErrorException('sql error: '. $e->getMessage(). ' ## ' . $sqlString . '##' . json_encode($sqlParams, JSON_UNESCAPED_UNICODE));
            } catch (\Exception $e) {
                throw new InsertErrorException('sql error: '. $e->getMessage(). ' ## ' . $sqlString . '##' . json_encode($sqlParams, JSON_UNESCAPED_UNICODE));
            }
        }


        /**
         * @param      $sqlString
         * @param      $sqlParams
         * @param null $returning
         *
         * @return int
         * @throws UpdateErrorException
         */
        public function update($sqlString, $sqlParams, $returning=null)
        {
            try {
                $st = $this->connection->prepare($sqlString);
                $res = $st->execute($this->fixBooleanFalse($sqlParams));

                if ($res === false) {
                    $info = $st->errorInfo();
                    throw new UpdateErrorException($info[0] . ': ' . $info[2] . ' (' . $info[1] . ')');
                }
                return $st->rowCount();
            } catch (\Exception $e) {
                throw new UpdateErrorException('sql error: '. $e->getMessage(). ' ## ' . $sqlString . '##' . json_encode($sqlParams, JSON_UNESCAPED_UNICODE));
            }
        }


        /**
         * @param      $sqlString
         * @param      $sqlParams
         * @param null $returning
         *
         * @return int
         * @throws DeleteErrorException
         */
        public function delete($sqlString, $sqlParams, $returning=null)
        {
            try {
                $st = $this->connection->prepare($sqlString);
                $res = $st->execute($this->fixBooleanFalse($sqlParams));

                if ($res === false) {
                    $info = $st->errorInfo();
                    throw new DeleteErrorException($info[0] . ': ' . $info[2] . ' (' . $info[1] . ')');
                }
                return $st->rowCount();
            } catch (\Exception $e) {
                throw new DeleteErrorException('sql error: '. $e->getMessage(). ' ## ' . $sqlString . '##' . json_encode($sqlParams, JSON_UNESCAPED_UNICODE));
            }
        }
    }