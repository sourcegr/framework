<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\Database;


    use Sourcegr\Framework\Database\PDOConnection\PDOConnection;
    use PDO;


    class DBConnectionManager implements DBConnectionManagerInterface
    {
        protected $connections;
        protected $defaultConnection = 'default';

        public function getDefaultConnection(): ?PDOConnection
        {
            return $this->getConnection($this->defaultConnection);
        }


        /**
         * @param string $defaultConnection
         */
        public function setDefaultConnection(string $defaultConnection = 'default'): void
        {
            $this->defaultConnection = $defaultConnection;
        }


        /**
         * @param $name
         *
         * @return PDO|null
         */
        public function getConnection(string $name) : ?PDOConnection {
            $PDOConnection = $this->connections[$name] ?? null;
            return $PDOConnection ?? null;
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
                    $config['user'] ?? null,
                    $config['password'] ?? null,
                    $config['pdo_params'] ?? []
                );

                $grammarClassName = __NAMESPACE__ . "\\" . ucfirst(strtolower($driver)).'Grammar';

                $grammar = new $grammarClassName($PDOConnection->connection);
                $PDOConnection->setGrammar($grammar);

                $this->setPostParams($PDOConnection, $driver, $config);
                $this->connections[$name] = $PDOConnection;

                return $PDOConnection;

            } catch(\Exception $e){
                throw new DBConnectionErrorException($e->getMessage());
            }
        }


        /**
         * @param string $driver
         * @param array  $config
         *
         * @return string
         */
        protected function getConnectionString(string $driver, array $config) {
            switch ($driver) {
                case 'mysql':
                    $host = $config['host'] ?? '127.0.0.1';
                    $db = $config['db'] ?? 'test';
                    $port = $config['port'] ?? 3386;
                    return "mysql:host=$host;port=$port;dbname=$db";

                case 'postgress':
                    return '';

                case 'dummy':
                    return 'sqlite::memory:';
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

            $encoding = $config['encoding'] ?? 'UTF8mb4';

            switch ($driver) {
                case 'mysql':
                    $PDOConnection->connection->setAttribute(PDO::MYSQL_ATTR_FOUND_ROWS, true);
                    // todo - run SET NAMES '$encoding' on MySQL Connection
                    return;

                case 'postgress':
                    return '';
            }
        }


    }