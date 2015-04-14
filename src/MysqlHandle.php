<?php

require_once 'ADatabaseHandle.php';

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

    public function defineTable(array $structure)
    {
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
	return implode(',', $nameType);
    }

    private function createTable($nameTypeStr)
    {
	try {
	    $this->pdoHandle->exec("CREATE TABLE IF NOT EXISTS {$this->tableName} ($nameTypeStr);");
	} catch (PDOException $e) {
	    print_r($e->getMessage());
	}
    }

    public function getInsertStatement()
    {
	$tableHeaderArray = explode(',', $this->tableHeader);
	$fieldCount = count($tableHeaderArray);
	$placeholder = implode(',', array_fill(0, $fieldCount, '?'));
	$insertStatement = 'INSERT INTO ' . $this->tableName
		. ' (' . $this->tableHeader . ') '
		. 'VALUES (' . $placeholder . ') '
		. 'ON DUPLICATE KEY UPDATE ';

	$updateDuplicates = [];
	foreach ($tableHeaderArray as $field) {
	    $updateDuplicates[] = "$field=VALUES($field)";
	}

	$insertStatement .= implode(',', $updateDuplicates) . ';';

	return $insertStatement;
    }

}
