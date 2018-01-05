<?php
namespace spec\Ozdemir\Datatables;

use Ozdemir\Datatables\DB\MySQL;
use Ozdemir\Datatables\DB\SQLite;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class DatatablesSpec extends ObjectBehavior {

    function let()
    {
        $sqlconfig = realpath(dirname(__FILE__) . '/test.db');
        $db = new SQLite($sqlconfig);

        $this->beConstructedWith($db);
    }

    public function getMatchers()
    {
        return [
            'haveColumns' => function ($subject, $key)
            {
                return (array_keys($subject) === $key);
            }
        ];
    }

    public function it_returns_record_counts()
    {
        $this->query("Select id as fid, name, surname, age from mytable where id > 3");
        $datatables = $this->generate(false);
        $datatables['recordsTotal']->shouldReturn(8);
        $datatables['recordsFiltered']->shouldReturn(8);
    }

    public function it_returns_data_from_a_basic_sql()
    {
        $this->query("Select id as fid, name, surname, age from mytable");

        $data = $this->generate(false)['data'][0];

        $data['fid']->shouldReturn("1");
        $data['name']->shouldReturn("John");
        $data['surname']->shouldContain('Doe');
    }

    public function it_sets_column_names_from_aliases()
    {
        $this->query("Select
                  film_id as fid,
                  title,
                  'description' as info,
                  release_year 'r_year',
                  film.rental_rate,
                  film.length as mins
            from film");
        $this->get('columns')->shouldReturn(['fid', 'title', 'info', 'r_year', 'rental_rate', 'mins']);
    }

    public function it_returns_modified_data_via_closure_function()
    {
        $this->query("Select id as fid, name, surname, age from mytable");

        $this->edit('name', function ($data)
        {
            return strtolower($data['name']);
        });

        $this->edit('surname', function ($data)
        {
            return $this->customfunction($data['surname']);
        });

        $data = $this->generate(false)['data']['2'];

        $data['name']->shouldReturn('george');
        $data['surname']->shouldReturn('Mar...');
    }

    function customfunction($data)
    {
        return substr($data, 0, 3) . '...';
    }

    public function it_returns_column_names_from_query_that_includes_a_subquery_in_select_statement()
    {
        $dt = $this->query( "SELECT column_name,
            (SELECT group_concat(cp.GRANTEE)
            FROM COLUMN_PRIVILEGES cp
            WHERE cp.TABLE_SCHEMA = COLUMNS.TABLE_SCHEMA
            AND cp.TABLE_NAME = COLUMNS.TABLE_NAME
            AND cp.COLUMN_NAME = COLUMNS.COLUMN_NAME)
            privs
            FROM COLUMNS
            WHERE table_schema = 'mysql' AND table_name = 'user';");

        $dt->get('columns')->shouldReturn(['column_name', 'privs']);
    }

    public function it_returns_column_names_from_query_that_includes_a_subquery_in_where_statement()
    {
        $dt = $this->query( "SELECT column_name
            FROM COLUMNS
            WHERE table_schema = 'mysql' AND table_name = 'user'
            and (SELECT group_concat(cp.GRANTEE)
            FROM COLUMN_PRIVILEGES cp
            WHERE cp.TABLE_SCHEMA = COLUMNS.TABLE_SCHEMA
            AND cp.TABLE_NAME = COLUMNS.TABLE_NAME
            AND cp.COLUMN_NAME = COLUMNS.COLUMN_NAME) is not null;");

        $dt->get('columns')->shouldReturn(['column_name']);
    }
}