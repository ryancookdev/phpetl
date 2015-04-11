<?php

namespace PhpEtl\Handle;

/**
 * Description of HandleFactory
 *
 * @author Ryan Cook <ryan@ryancook.software>
 */
class HandleFactory
{

    public static function createHandle(array $config = NULL)
    {
	if (is_array($config)) {
	    if (key_exists('type', $config)) {
		$type = '\\PhpEtl\\Handle\\Database\\' . $config['type'] . 'Handle';
		return new $type($config);
	    }
	}

	return new \PhpEtl\Stage([], []);
    }

}
