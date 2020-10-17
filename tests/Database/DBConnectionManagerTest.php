<?php

    namespace Sourcegr\Tests\Database;

    use PDO;
    use Sourcegr\Framework\Database\DBConnectionManager;
    use PHPUnit\Framework\TestCase;
    use Sourcegr\Framework\Database\PDOConnection\PDOConnection;

    class DBConnectionManagerTest extends TestCase
    {

        public function testCreate($name = 'n', $driver = 'mysql') {


            $config = [
                'USER' => 'root',
                'PASSWORD' => 'root',
                'PDO_PARAMS' => [
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8mb4'"
                ],
                'HOST' => '127.0.0.1',
                'PORT' => 3306,
                'DB' => 'test'
            ];
            $manager = new DBConnectionManager();
            $result = $manager->create($name, $driver, $config);

            self::assertInstanceOf(PDOConnection::class, $result);
            self::assertInstanceOf(PDO::class, $result->connection);

            return $manager;
        }

        public function testGetConnection()
        {
            $name = 'myname';
            $driver = 'mysql';

            $manager = $this->testCreate($name, $driver);
            $result = $manager->getConnection($name);

            self::assertInstanceOf(PDO::class, $result);
        }
    }
