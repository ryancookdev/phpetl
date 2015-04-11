<?php

namespace PhpEtl\Handle\Database;

/**
 * Description of MysqlHandle
 *
 * @author Ryan Cook <ryan@ryancook.software>
 */
class MysqlHandle extends \PhpEtl\Handle\ADatabaseHandle
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

    public function close()
    {
	// Do nothing
    }

    public function define(array $structure = NULL)
    {
	// Do nothing
    }

    public function translateTypes(array $structure)
    {
	// Do nothing
    }

    public function typeMap($type)
    {
	// Do nothing
    }

}
