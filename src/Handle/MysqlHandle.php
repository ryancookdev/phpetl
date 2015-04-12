<?php

namespace PhpEtl;

/**
 * Description of MysqlHandle
 *
 * @author Ryan Cook <ryan@ryancook.software>
 */
class MysqlHandle extends ADatabaseHandle
{

    public function getDsn($host, $database)
    {
	return $dbh = "mysql:host=$host;dbname=$database";
    }

    public function beginTransaction()
    {
	$this->pdoHandle->beginTransaction();
    }

    public function commitTransaction()
    {
	$this->pdoHandle->commit();
    }

    public function translateTypes(array $structure)
    {
	foreach ($structure as $name => &$type) {
	    $type = $this->typeMap($type);
	}
	return $structure;
    }

    public function typeMap($type)
    {
	// TODO: Create map. This will fail for non-MySQL sources
	return $type;
    }

    public function defineTable($name, array $structure)
    {
	$this->tableName = $name;
	$this->setTableHeader(array_keys($structure));
	$translatedStructure = $this->translateTypes($structure);
	$nameTypeStr = $this->structureToString($translatedStructure);
	$this->createTable($nameTypeStr);
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
	    $this->pdoHandle->exec("CREATE TABLE IF NOT EXISTS {$this->tableName} ($nameTypeStr);");
	} catch (PDOException $e) {
	    print_r($e->getMessage());
	}
    }

}
