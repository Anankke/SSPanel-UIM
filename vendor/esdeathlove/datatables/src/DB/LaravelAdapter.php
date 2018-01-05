<?php namespace Ozdemir\Datatables\DB;

use DB;

class LaravelAdapter implements DatabaseInterface {

    protected $escape = [];

    function __construct($config = null)
    {
    }

    public function connect()
    {
        return $this;
    }

    public function query($query)
    {
        $data = DB::select( $query, $this->escape);
        $row = [];

        foreach ($data as $item)
        {
            $row[] = (array) $item;
        }

        return $row;
    }

    public function count($query)
    {
        $query = "Select count(*) as rowcount," . substr($query, 6);
        $data = DB::select( $query, $this->escape);

        return $data[0]->rowcount;
    }

    public function escape($string)
    {
        $this->escape[':escape' . (count($this->escape) + 1) ] = '%' . $string . '%';

        return ":escape" . (count($this->escape));
    }

}