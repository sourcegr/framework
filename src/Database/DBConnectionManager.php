<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\Database;


    use Sourcegr\Framework\Database\PDOConnection\PDOConnection;
    use PDO;


    class DBConnectionManager implements DBConnectionManagerInterface
    {
        protected $connections;

        /**
         * @param $name
         *
         * @return PDO|null
         */
        public function getConnection(string $name) : ?PDO {
            $PDOConnection = $this->connections[$name] ?? null;
            return $PDOConnection->connection ?? null;
        }


        /**
         * @param string $name
         * @param string $driver
         * @param array  $config
         *
         * @return PDOConnection
         * @throws DBConnectionErrorException
         */
        public function create(string $name, string $driver, array $config) : PDOConnection {
            $connectionString = $this->getConnectionString($driver, $config);

            try {
                $PDOConnection = new PDOConnection(
                    $connectionString,
                    $config['USER'],
                    $config['PASSWORD'],
                    $config['PDO_PARAMS']
                );

                $this->setPostParams($PDOConnection, $driver, $config);
                $this->connections[$name] = $PDOConnection;

                return $PDOConnection;

            } catch(\Exception $e){
                throw new DBConnectionErrorException("Cannot create named connection $name");
            }
        }


        /**
         * @param string $driver
         * @param array  $config
         *
         * @return string
         */
        protected function getConnectionString(string $driver, array $config) {
            $host = $config['HOST'];
            $port = $config['PORT'];
            $db = $config['DB'];

            switch ($driver) {
                case 'mysql':
                    return "mysql:host=$host;port=$port;dbname=$db";

                case 'postgress':
                    return '';
            }
        }


        /**
         * sets parameters for the PDOConnection's PDO
         *
         * @param PDOConnection $PDOConnection
         * @param string        $driver
         * @param array         $config
         *
         * @return string|void
         */
        protected function setPostParams(PDOConnection $PDOConnection, string $driver, array $config) {

            $encoding = $config['ENCODING'] ?? 'UTF8mb4';

            switch ($driver) {
                case 'mysql':
                    $PDOConnection->connection->setAttribute(PDO::MYSQL_ATTR_FOUND_ROWS, true);
                    return;

                case 'postgress':
                    return '';
            }
        }
    }