<?php namespace Ozdemir\Datatables\DB;

use PDO;
use PDOException;

class SQLite implements DatabaseInterface {

    protected $pdo;
    protected $config;
    protected $escape = [];

    function __construct($config)
    {
        $this->config = $config;
    }

    public function connect()
    {
        try {
            $this->pdo = new PDO('sqlite:' . $this->config);
        } catch ( PDOException $e ){
            print $e->getMessage();
        }
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        return $this;
    }

    public function query($query)
    {
        $sql = $this->pdo->prepare($query);
        $rows=$sql->execute($this->escape);
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function count($query)
    {
        $sql = $this->pdo->prepare($query);
        $rows = $sql->execute($this->escape);
        return count($sql->fetchAll());
    }

    public function escape($string)
    {
        $this->escape[':escape' . (count($this->escape) + 1) ] = '%' . $string . '%';
        return ":escape" . (count($this->escape));
    }

}