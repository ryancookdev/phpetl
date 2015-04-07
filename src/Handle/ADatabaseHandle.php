<?php

namespace PhpEtl\Handle;

abstract class ADatabaseHandle implements IHandle
{

    protected $pdo_handle;
    protected $table_header;
    protected $table_name;
    protected $update_fields;
    protected $current_internal_row_id;
    protected $max_insert = 500;

    public function __construct(array $conn, array $data)
    {
	$host = NULL;
	$user = NULL;
	$password = NULL;
	$database = NULL;

	if (key_exists('host', $conn)) {
	    $host = $conn['host'];
	}
	if (key_exists('user', $conn)) {
	    $user = $conn['user'];
	}
	if (key_exists('password', $conn)) {
	    $password = $conn['password'];
	}
	if (key_exists('database', $conn)) {
	    $database = $conn['database'];
	}
	if (key_exists('table', $data)) {
	    $this->table_name = $data['table'];
	}
	if (key_exists('fields', $data)) {
	    $this->table_header = $data['fields'];
	}
	if (key_exists('update_fields', $data)) {
	    $this->update_fields = $data['update_fields'];
	}

	$dsn = $this->getDsn($host, $database);

	try {
	    $this->pdo_handle = new \PDO($dsn, $user, $password);
	    $this->pdo_handle->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
	} catch (PDOException $e) {
	    print_r($e->getMessage());
	}
    }

    public function extract(IHandle $destination, $sql = NULL)
    {
	$rows = [];
	$i = 0;

	$sql = ($sql === NULL ? 'SELECT ' . $this->table_header . ' FROM ' . $this->table_name : $sql);

	try {
	    $stmt = $this->pdo_handle->prepare($sql);
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
	$field_count = count(explode(',', $this->table_header));
	$placeholder = implode(',', array_fill(0, $field_count, '?'));
	// Prepared statements do not handle variable table/field names, so
	// part of the statement must be built with string concatenation.
	$sql = 'INSERT INTO ' . $this->table_name . ' (' . $this->table_header . ') VALUES (' . $placeholder . ');';

	try {
	    $stmt = $this->pdo_handle->prepare($sql);
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
	$this->pdo_handle = NULL;
    }

    abstract public function getDsn($host, $database);

    abstract public function close();

}
