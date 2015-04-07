<?php

namespace PhpEtl\Handle\Database;

/**
 * Description of SqliteHandle
 *
 * @author Ryan Cook <ryan@ryancook.software>
 */
class SqliteHandle extends \PhpEtl\Handle\ADatabaseHandle
{

    public function getDsn($host, $database)
    {
	return 'sqlite:' . STAGE_PATH . '/local.db';
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
	\unlink(STAGE_PATH . '/local.db');
    }

}
