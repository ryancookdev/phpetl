<?php

namespace PhpEtl;

/**
 * Description of Stage
 *
 * @author Ryan Cook <ryan@ryancook.software>
 */
class Stage extends Handle\Database\SqliteHandle implements IStage
{

    protected $table_name;
    protected $table_header;

    public function createTable($name, $header)
    {
	$this->table_name = $name;
	$this->table_header = $header;

	$header_type = explode(',', $header);
	array_walk($header_type, function (&$field, $key) {
	    $field = $field . ' text';
	});

	$header_type_str = implode(',', $header_type);

	try {
	    $this->pdo_handle->exec("DROP TABLE IF EXISTS {$this->table_name};");
	    $this->pdo_handle->exec("CREATE TABLE {$this->table_name} ('internal_row_id' integer primary key, $header_type_str);");
	} catch (PDOException $e) {
	    print_r($e->getMessage());
	}
    }

    public function getRow()
    {

    }

    public function setRow()
    {

    }

    public function select()
    {

    }

    public function setTable()
    {

    }

    public function splitColumn()
    {

    }

    public function createInternalTable()
    {

    }

    public function insertInternalTable()
    {

    }

}
