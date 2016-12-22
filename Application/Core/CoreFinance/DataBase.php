<?php
namespace Finance\Core\CoreFinance;

/**
 * Class DataBase
 *
 * @package Finance\CoreFinance
 */
class DataBase
{
    /**
     * @var DataBase
     */
    private static $instance;
    /**
     * @var string
     */
    private $host;
    /**
     * @var string
     */
    private $user;
    /**
     * @var string
     */
    private $password;
    /**
     * @var string
     */
    private $dataBaseName;

    /**
     * @var \mysqli
     */
    private $connection;

    /**
     * @param string $host
     * @param string $user
     * @param string $password
     * @param string $dataBaseName
     */
    public static function initDataBase(string $host, string $user, string $password, string $dataBaseName)
    {
        static::$instance = new self();
        static::$instance->host = $host;
        static::$instance->user = $user;
        static::$instance->password = $password;
        static::$instance->dataBaseName = $dataBaseName;
        static::$instance->connection = static::$instance->connect();
        static::$instance->assertSuccessConnection();
    }

    /**
     * @return DataBase
     * @throws \Exception
     */
    public static function getInstance(): self
    {
        if (static::$instance === null) {
            throw new \Exception("Object 'DataBase' not init.");
        }

        return static::$instance;
    }

    /**
     * @param string $query
     * @return array
     */
    public function selectQuery(string $query)
    {
        $query = $this->connection->query($query);
        $result = [];
        while ($row = $query->fetch_assoc()) {
            $result[] = $row;
        }

        return $result;
    }

    public function insertQuery(string $query)
    {
        $this->connection->query($query);
    }

    /**
     * @return \mysqli
     */
    private function connect()
    {
        return mysqli_connect(
            $this->host,
            $this->user,
            $this->password,
            $this->dataBaseName
        );
    }

    /**
     * @throws \Exception
     */
    private function assertSuccessConnection()
    {
        if ($this->connection->connect_errno) {
            throw new \Exception(printf("Не удалось подключиться: %s\n", $this->connection->connect_error));
        }
    }
}