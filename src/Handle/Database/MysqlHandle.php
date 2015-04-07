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
	$this->pdo_handle->beginTransaction();
    }

    public function commit()
    {
	$this->pdo_handle->commit();
    }

    public function close()
    {
	
    }

}
