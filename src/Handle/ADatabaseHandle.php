<?php

namespace PhpEtl\Handle;

abstract class ADatabaseHandle implements IHandle
{

    protected $pdoHandle;
    protected $tableHeader;
    protected $tableName;
    protected $updateFields;
    protected $currentRowId;
    protected $maxInsert = 500;

    public function __construct(array $connection, array $data)
    {
	$host = NULL;
	$user = NULL;
	$password = NULL;
	$database = NULL;

	if (key_exists('host', $connection)) {
	    $host = $connection['host'];
	}
	if (key_exists('user', $connection)) {
	    $user = $connection['user'];
	}
	if (key_exists('password', $connection)) {
	    $password = $connection['password'];
	}
	if (key_exists('database', $connection)) {
	    $database = $connection['database'];
	}
	if (key_exists('table', $data)) {
	    $this->tableName = $data['table'];
	}
	if (key_exists('fields', $data)) {
	    $this->tableHeader = $data['fields'];
	}
	if (key_exists('update_fields', $data)) {
	    $this->updateFields = $data['update_fields'];
	}

	$dsn = $this->getDsn($host, $database);

	try {
	    $this->pdoHandle = new \PDO($dsn, $user, $password);
	    $this->pdoHandle->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
	} catch (PDOException $e) {
	    print_r($e->getMessage());
	}
    }

    public function extract(IHandle $destination, $sql = NULL)
    {
	$rows = [];
	$i = 0;

	$sql = ($sql === NULL ? 'SELECT ' . $this->tableHeader . ' FROM ' . $this->tableName : $sql);

	try {
	    $stmt = $this->pdoHandle->prepare($sql);
	    $stmt->execute();

	    $destination->beginTransaction();

	    while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
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

	    $destination->commit();
	} catch (PDOException $e) {
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
	} catch (PDOException $e) {
	    print_r($e->getMessage());
	}
    }

    public function __destruct()
    {
	$this->close();
	$this->pdoHandle = NULL;
    }

    abstract public function getDsn($host, $database);

    abstract public function close();

}
