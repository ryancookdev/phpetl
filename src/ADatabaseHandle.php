<?php

require_once 'IHandle.php';
require_once 'View.php';

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
	    return new PDO($dsn, $user, $password);
	} catch (PDOException $e) {
	    print_r($e->getMessage());
	}
    }

    public function send(IHandle $destination, View $view)
    {
	$rows = [];
	$i = 0;

	try {
	    $stmt = $this->pdoHandle->prepare($view->query);
	    $stmt->execute();

	    $destination->defineTable($view->name, $this->getTableStructureFromQuery($view->query));

	    $destination->beginTransaction();

	    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
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
	} catch (PDOException $e) {
	    print_r($e->getMessage());
	}

	return $destination;
    }

    public function load(array $rows)
    {
	try {
	    $stmt = $this->pdoHandle->prepare($this->getInsertStatement());
	    foreach ($rows as $row) {
		$stmt->execute($row);
	    }
	} catch (PDOException $e) {
	    print_r($e->getMessage());
	}
    }

    public function tempTable(View $view)
    {
	$this->createTempTableFromQuery($view->name, $view->query);
    }

    public function setPDOAttributes()
    {
	$this->pdoHandle->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    private function getTableStructureFromQuery($query)
    {
	$this->createTempTableFromQuery('temp', $query, $empty = TRUE);
	$structure = $this->readTempTableStructure();
	$this->destroyTempTable();

	return $structure;
    }

    private function createTempTableFromQuery($name, $query, $empty = FALSE)
    {
	$create = "CREATE TEMPORARY TABLE $name AS ($query)";
	if ($empty) {
	    $create .= ' LIMIT 0';
	}
	$create .= ';';

	try {
	    $this->pdoHandle->exec($create);
	} catch (PDOException $e) {
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
	} catch (PDOException $e) {
	    print_r($e->getMessage());
	}
    }

    private function destroyTempTable()
    {
	try {
	    $this->pdoHandle->exec('DROP TEMPORARY TABLE IF EXISTS temp;');
	} catch (PDOException $e) {
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

    abstract public function getInsertStatement();

}
