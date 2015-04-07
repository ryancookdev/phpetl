<?php

namespace PhpEtl;

/**
 * Description of PhpEtl
 *
 * @author Ryan Cook <ryan@ryancook.software>
 */
class PhpEtl
{

    public function __construct()
    {
	spl_autoload_extensions('.php');
	spl_autoload_register();
    }

}

$phpetl = new PhpEtl();
