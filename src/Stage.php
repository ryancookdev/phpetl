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

    public function define(array $structure)
    {
	$this->setTableHeader(array_keys($structure));
	$translatedStructure = $this->translateTypes($structure);
	$nameTypeStr = $this->structureToString($translatedStructure);
	$this->createTable($nameTypeStr);
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

    public function setTableName($name)
    {
	$this->tableName = $name;
    }

    private function setTableHeader(array $header)
    {
	$this->tableHeader = implode(',', $header);
    }

    private function structureToString(array $structure)
    {
	$nameType = [];
	foreach ($structure as $name => $type) {
	    $nameType[] = "$name $type";
	}
	return \implode(',', $nameType);
    }

    private function createTable($nameTypeStr)
    {
	try {
	    $this->pdoHandle->exec("DROP TABLE IF EXISTS {$this->tableName};");
	    $this->pdoHandle->exec("CREATE TABLE {$this->tableName} ('internal_row_id' integer primary key, $nameTypeStr);");
	} catch (PDOException $e) {
	    print_r($e->getMessage());
	}
    }

}
