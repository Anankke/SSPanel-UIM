<?php namespace Ozdemir\Datatables\DB;

class CodeigniterAdapter implements DatabaseInterface {

    protected $escape = [];
    protected $CI;

    function __construct($config = null)
    {
        $this->CI =& get_instance();
        $this->CI->load->database();
    }

    public function connect()
    {
        return $this;
    }

    public function query($query)
    {
        $data =  $this->CI->db->query($query, $this->escape);

        return $data->result_array();
    }

    public function count($query)
    {
        $query = "Select count(*) as rowcount from ($query)t";
        $data =  $this->CI->db->query($query, $this->escape)->result_array();

        return $data[0]['rowcount'];
    }

    public function escape($string)
    {
        $this->escape[] = '%' . $string . '%';

        return "?";
    }

}