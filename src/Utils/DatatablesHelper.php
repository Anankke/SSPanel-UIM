<?php

declare(strict_types=1);

namespace App\Utils;

use App\Services\Config;
use Illuminate\Database\Capsule\Manager as Capsule;
use Ozdemir\Datatables\DB\DatabaseInterface;

final class DatatablesHelper implements DatabaseInterface
{
    private $escape = [];
    private $connection;

    public function __construct($config = null)
    {
        $capsule = new Capsule();
        $capsule->addConnection(Config::getDbConfig(), 'default');
        $this->connection = $capsule->getConnection('default');
        $this->connection->query("set session sql_mode='';");
    }

    public function connect()
    {
        return $this;
    }

    public function query($query)
    {
        $data = $this->connection->select($query, $this->escape);
        $row = [];
        foreach ($data as $item) {
            $row[] = (array) $item;
        }
        return $row;
    }

    public function count($query)
    {
        $query = 'Select count(*) as rowcount,' . substr($query, 6);
        $data = $this->connection->select($query, $this->escape);
        return $data[0]->rowcount;
    }

    public function escape($string)
    {
        $this->escape[':escape' . (count($this->escape) + 1)] = '%' . $string . '%';
        return ':escape' . count($this->escape);
    }
}
