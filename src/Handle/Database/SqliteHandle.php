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
	$this->pdoHandle->beginTransaction();
    }

    public function commitTransaction()
    {
	$this->pdoHandle->commit();
    }

    public function close()
    {
	\unlink(STAGE_PATH . '/local.db');
    }

    public function define(array $structure)
    {
	// Do nothing;
    }

    public function translateTypes(array $structure)
    {
	foreach ($structure as $name => &$type) {
	    $type = $this->typeMap($type);
	}
	return $structure;
    }

    public function typeMap($type)
    {
	return 'text';
    }

}
