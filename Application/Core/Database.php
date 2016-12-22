<?php
namespace Finance\Core;

use Finance\Core\Exceptions\ApplicationException;

/**
 * Class Database
 *
 * @package Finance\Core
 */
class Database
{
    /**
     * @var Database
     */
    private static $instance;
    private $connection;


    public static function init(string $host, string $user, string $password, string $dataBaseName)
    {
        static::$instance = new self();
        static::$instance->connection = static::$instance->createConnection($host, $user, $password, $dataBaseName);

    }

    /**
     * @return Database
     * @throws ApplicationException
     */
    public static function getInstance(): Database
    {
        if (static::$instance === null) {
            throw new ApplicationException("Объект 'Database' не инициализирован! ");
        }

        return static::$instance;
    }

    /**
     * @param string $host
     * @param string $user
     * @param string $password
     * @param string $dataBaseName
     * @return \mysqli
     */
    private function createConnection(string $host, string $user, string $password, string $dataBaseName): \mysqli
    {
        $mysqli = mysqli_connect($host, $user, $password, $dataBaseName);
        $mysqli->set_charset('utf8');

        return $mysqli;
    }

    /**
     * @param string $query
     * @return array
     */
    public function query(string $query)
    {
        $mysqliQuery = mysqli_query($this->connection, $query);
        $result = [];
        while ($row = mysqli_fetch_assoc($mysqliQuery)) {
            $result[] = $row;
        }

        return $result;
    }
}