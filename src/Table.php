<?php

namespace PhpEtl;

class Table implements \Iterator
{

    // The current position during iteration
    protected $position = 0;
    protected $stage;

    // The data for the current position during iteration
    //protected $current_row = array();

    public function __construct(array $data)
    {
	$this->position = 0;
	$this->stage = Handle\HandleFactory::createHandle();
	$this->stage->createTable($data['table'], $data['fields']);
    }

    // Proxy to IHandle methods
    // Move data to the stage
    public function extract(array $source_conn, array $source_data)
    {
	$source = Handle\HandleFactory::createHandle($source_conn, $source_data);

	if (key_exists('sql', $source_data)) {
	    $source->extract($this->stage, $source_data['sql']);
	} else {
	    $source->extract($this->stage);
	}
    }

    // Move data from the stage
    public function load(array $dest_conn, array $dest_data)
    {
	$destination = Handle\HandleFactory::createHandle($dest_conn, $dest_data);
	$this->stage->extract($destination);
    }

    // Proxy to IStage methods

    public function setTable()
    {

    }

    public function transform()
    {

    }

    public function transformRow()
    {

    }

    public function splitColumn()
    {

    }

    // Implement Iterator methods

    function rewind()
    {
	$this->position = 0;
    }

    function current()
    {

    }

    function key()
    {
	return $this->position;
    }

    function next()
    {
	++$this->position;
    }

    function valid()
    {

    }

}
