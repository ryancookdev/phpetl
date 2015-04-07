<?php

namespace PhpEtl\Handle;

/**
 * Description of HandleFactory
 *
 * @author Ryan Cook <ryan@ryancook.software>
 */
class HandleFactory
{

    public static function createHandle(array $conn = NULL, array $data = NULL)
    {
	if (is_array($conn)) {
	    if (key_exists('type', $conn)) {
		$type = '\\PhpEtl\\Handle\\Database\\' . $conn['type'] . 'Handle';
		return new $type($conn, $data);
	    }
	}

	return new \PhpEtl\Stage([], []);
    }

}
