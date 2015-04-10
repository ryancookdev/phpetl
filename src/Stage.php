<?php

namespace PhpEtl;

/**
 * Description of Stage
 *
 * @author Ryan Cook <ryan@ryancook.software>
 */
class Stage extends Handle\Database\SqliteHandle implements IStage
{

    protected $tableName;
    protected $tableHeader;

    public function createTable($name, $header)
    {
	$this->tableName = $name;
	$this->tableHeader = $header;

	$headerType = explode(',', $header);
	array_walk($headerType, function (&$field, $key) {
	    $field = $field . ' text';
	});

	$header_type_str = implode(',', $headerType);

	try {
	    $this->pdoHandle->exec("DROP TABLE IF EXISTS {$this->tableName};");
	    $this->pdoHandle->exec("CREATE TABLE {$this->tableName} ('internal_row_id' integer primary key, $header_type_str);");
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
