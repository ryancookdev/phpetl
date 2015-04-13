<?php

/**
 * Description of SqliteHandle
 *
 * @author Ryan Cook <ryan@ryancook.software>
 */
class SqliteHandle extends ADatabaseHandle
{

    public function getDsn($host, $database)
    {
	return 'sqlite:' . STAGE_PATH . '/local.db';
    }

    public function beginTransaction()
    {
	$this->pdoHandle->beginTransaction();
    }

    public function commitTransaction()
    {
	$this->pdoHandle->commit();
    }

    public function defineTable($name, array $structure)
    {

    }

    public function translateTypes(array $structure)
    {

    }

    public function typeMap($type)
    {

    }

    protected function close()
    {
	parent::close();
	unlink(STAGE_PATH . '/local.db');
    }

    public function getInsertStatement()
    {

    }

}
