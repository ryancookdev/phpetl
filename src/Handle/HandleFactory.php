<?php

namespace PhpEtl\Handle;

/**
 * Description of HandleFactory
 *
 * @author Ryan Cook <ryan@ryancook.software>
 */
class HandleFactory
{

    public static function createHandle(array $connection = NULL, array $data = NULL)
    {
	if (is_array($connection)) {
	    if (key_exists('type', $connection)) {
		$type = '\\PhpEtl\\Handle\\Database\\' . $connection['type'] . 'Handle';
		return new $type($connection, $data);
	    }
	}

	return new \PhpEtl\Stage([], []);
    }

}
