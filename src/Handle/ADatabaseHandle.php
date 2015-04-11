<?php

namespace PhpEtl\Handle;

abstract class ADatabaseHandle implements IHandle
{

    protected $pdoHandle;
    protected $tableHeader;
    protected $tableName;
    protected $updateFields;
    protected $sql;
    protected $currentRowId;
    protected $maxInsert = 500;

    public function __construct(array $config)
    {
	$host = (key_exists('host', $config) ? $config['host'] : NULL);
	$user = (key_exists('user', $config) ? $config['user'] : NULL);
	$password = (key_exists('password', $config) ? $config['password'] : NULL);
	$database = (key_exists('database', $config) ? $config['database'] : NULL);

	if (key_exists('table', $config)) {
	    $this->tableName = $config['table'];
	}
	if (key_exists('fields', $config)) {
	    $this->tableHeader = $config['fields'];
	}
	if (key_exists('update_fields', $config)) {
	    $this->updateFields = $config['update_fields'];
	}
	if (key_exists('sql', $config)) {
	    $this->sql = $config['sql'];
	}

	$dsn = $this->getDsn($host, $database);

	try {
	    $this->pdoHandle = new \PDO($dsn, $user, $password);
	    $this->pdoHandle->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
	} catch (\PDOException $e) {
	    print_r($e->getMessage());
	}
    }

    public function extract(IHandle $destination)
    {
	$rows = [];
	$i = 0;

	$sql = ($this->sql === NULL ? 'SELECT ' . $this->tableHeader . ' FROM ' . $this->tableName : $this->sql);

	try {
	    $stmt = $this->pdoHandle->prepare($sql);
	    $stmt->execute();

	    $destination->beginTransaction();

	    $destination->define(($this->getTableStructureFromQuery($sql)));

	    while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
		// FIX... only relevant for Stage
		unset($row['internal_row_id']);
		$rows[] = array_values($row);
		if (++$i >= 500) {
		    $destination->load($rows);
		    $rows = [];
		}
	    }

	    if (count($rows) > 0) {
		$destination->load($rows);
	    }

	    $destination->commitTransaction();
	} catch (\PDOException $e) {
	    print_r($e->getMessage());
	}
    }

    public function load(array $rows)
    {
	$fieldCount = count(explode(',', $this->tableHeader));
	$placeholder = implode(',', array_fill(0, $fieldCount, '?'));
	// Prepared statements do not handle variable table/field names, so
	// part of the statement must be built with string concatenation.
	$sql = 'INSERT INTO ' . $this->tableName . ' (' . $this->tableHeader . ') VALUES (' . $placeholder . ');';

	try {
	    $stmt = $this->pdoHandle->prepare($sql);
	    foreach ($rows as $row) {
		$stmt->execute($row);
	    }
	} catch (\PDOException $e) {
	    print_r($e->getMessage());
	}
    }

    private function getTableStructureFromQuery($sql)
    {
	$this->createTempTableFromQuery($sql);
	return $this->readTempTableStructure();
    }

    private function createTempTableFromQuery($sql)
    {
	try {
	    $this->pdoHandle->exec('CREATE TEMPORARY TABLE temp AS (' . $sql . ') LIMIT 0; DESCRIBE temp;');
	} catch (\PDOException $e) {
	    print_r($e->getMessage());
	}
    }

    private function readTempTableStructure()
    {
	try {
	    $structure = [];
	    foreach ($this->pdoHandle->query('DESCRIBE temp;') as $row) {
		$structure[$row['Field']] = $row['Type'];
	    }
	    return $structure;
	} catch (\PDOException $e) {
	    print_r($e->getMessage());
	}
    }

    public function __destruct()
    {
	$this->close();
	$this->pdoHandle = NULL;
    }

    abstract public function getDsn($host, $database);

    abstract public function translateTypes(array $structure);

    abstract public function typeMap($type);

    abstract public function close();

}
