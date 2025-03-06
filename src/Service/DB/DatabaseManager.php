<?php

namespace App\Service\DB;

use Exception;
use PDO;
use PDOException;
use Symfony\Component\Dotenv\Dotenv;

class DatabaseManager
{
    private ?PDO $connection = null;

    public function getEntityManager(): EntityManager
    {
        try {
            return new EntityManager();
        } catch (Exception $e) {
            var_dump('Missing Database driver', $e);
            exit();
        }
    }

    /**
     * @throws Exception
     */
    public function connect(): PDO
    {
        if ($this->connection === null) {
            $dsn = 'mysql:host='.getenv("MYSQL_HOST").';dbname='.getenv("MYSQL_DATABASE").';charset=utf8mb4';
            $username = getenv("MYSQL_USER");
            $password = getenv("MYSQL_PASSWORD");
            try {
                $this->connection = new PDO($dsn, $username, $password);
                $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("Database connection failed: " . $e->getMessage());
            }
        }

        return $this->connection;
    }

    // private function getDatabaseParameters(): array
    // {
    //     $dotenv = new Dotenv();
    //     try {
    //         $dotenv->loadEnv(__DIR__ . '/../../../.env');
    //     } catch (Exception $e) {
    //         var_dump($e);
    //     }

    //     return [
    //         'user'     => "root",
    //         'password' => "rootpassword",
    //         'dbname'   => "bddexampls",
    //     ];
    // }
}
