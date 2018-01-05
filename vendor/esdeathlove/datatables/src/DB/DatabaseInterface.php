<?php

namespace Ozdemir\Datatables\DB;

interface DatabaseInterface {

    public function __construct($config);

    public function connect();

    public function query($query);

    public function count($query);

    public function escape($string);

}
