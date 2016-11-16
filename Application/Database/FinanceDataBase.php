<?php

namespace Finance\Application\Database;

use Exception;
use mysqli;

class FinanceDataBase
{
    private $host = 'localhost';
    private $database = 'finance'; // имя базы данных
    private $user = 'root'; // имя пользователя
    private $password = 'root'; // пароль
    private $port = 3306;
    /**
     * @var mysqli
     */
    private $mysqli;

    /**
     * FinanceDataBaseConnect constructor.
     */
    public function __construct()
    {
        $this->connect();
    }

    private function connect()
    {
        $this->mysqli = new mysqli(
            $this->host,
            $this->user,
            $this->password,
            $this->database,
            $this->port
        );
        $this->assertSuccessConnection();

    }

    /**
     * @param string $query
     * @return mixed
     */
    public function getAll(string $query)
    {
        return $this->mysqli->query($query)->fetch_all();
    }

    /**
     * @param string $query
     * @return mixed
     */
    public function getOneRow(string $query)
    {
        return $this->mysqli->query($query)->fetch_assoc();
    }


    /**
     * @param string $query
     * @return bool
     */
    public function makeQuery(string $query):bool
    {
        return $this->mysqli->query($query);
    }

    /**
     * @throws Exception
     */
    private function assertSuccessConnection()
    {
        if ($this->mysqli->connect_errno) {
            throw new Exception(printf("Не удалось подключиться: %s\n", $this->mysqli->connect_error));
        }
    }
}
