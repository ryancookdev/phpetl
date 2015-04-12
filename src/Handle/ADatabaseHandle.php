<?php

namespace PhpEtl;

abstract class ADatabaseHandle implements IHandle
{

    protected $pdoHandle;
    protected $tableHeader;
    protected $tableName;
    protected $maxInsert = 500;

    public function __construct(array $config)
    {
	$dsn = $this->getDsn($config['host'], $config['database']);
	$this->pdoHandle = $this->createPDOHandle($dsn, $config['user'], $config['password']);
	$this->setPDOAttributes();
    }

    private function createPDOHandle($dsn, $user, $password)
    {
	try {
	    return new \PDO($dsn, $user, $password);
	} catch (\PDOException $e) {
	    print_r($e->getMessage());
	}
    }

    public function send(IHandle $destination, array $table)
    {
	$rows = [];
	$i = 0;

	try {
	    $stmt = $this->pdoHandle->prepare($table['query']);
	    $stmt->execute();

	    $destination->defineTable($table['name'], $this->getTableStructureFromQuery($table['query']));

	    $destination->beginTransaction();

	    while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
		$rows[] = array_values($row);
		if (++$i >= 500) {
		    $i = 0;
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

	return $destination;
    }

    public function load(array $rows)
    {
	$fieldCount = count(explode(',', $this->tableHeader));
	$placeholder = implode(',', array_fill(0, $fieldCount, '?'));
	// Prepared statements do not handle variable table/field names, so
	// part of the statement must be built with string concatenation.
	$query = 'INSERT INTO ' . $this->tableName . ' (' . $this->tableHeader . ') VALUES (' . $placeholder . ');';

	try {
	    $stmt = $this->pdoHandle->prepare($query);
	    foreach ($rows as $row) {
		$stmt->execute($row);
	    }
	} catch (\PDOException $e) {
	    print_r($e->getMessage());
	}
    }

    public function setPDOAttributes()
    {
	$this->pdoHandle->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    private function getTableStructureFromQuery($query)
    {
	$this->createTempTableFromQuery($query);
	return $this->readTempTableStructure();
    }

    private function createTempTableFromQuery($query)
    {
	try {
	    $this->pdoHandle->exec('CREATE TEMPORARY TABLE temp AS (' . $query . ') LIMIT 0; DESCRIBE temp;');
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

    protected function close()
    {
	$this->pdoHandle = NULL;
    }

    public function __destruct()
    {
	$this->close();
    }

    abstract public function getDsn($host, $database);

    abstract public function translateTypes(array $structure);

    abstract public function typeMap($type);

}
