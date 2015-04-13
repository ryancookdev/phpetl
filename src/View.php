<?php

/**
 * Description of View
 *
 * @author Ryan Cook <ryan@ryancook.software>
 */
class View
{

    public $name;
    public $query;

    public function __construct($name, $query)
    {
	$this->name = $name;
	$this->query = $query;
    }

}
