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

    public function commit()
    {
	$this->pdoHandle->commit();
    }

    public function close()
    {
	
    }

}
