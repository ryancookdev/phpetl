<?php

namespace PhpEtl;

class Table implements \Iterator
{

    protected $stage;
    protected $iteratorCurrentPosition = 0;

    public function __construct($name)
    {
	$this->stage = Handle\HandleFactory::createHandle();
	$this->stage->setTableName($name);
    }

    // Move data to the stage
    public function extract(array $sourceConfig)
    {
	$sourceHandle = Handle\HandleFactory::createHandle($sourceConfig);
	$sourceHandle->extract($this->stage);
    }

    // Move data from the stage
    public function load(array $destinationConfig)
    {
	$destinationHandle = Handle\HandleFactory::createHandle($destinationConfig);
	$this->stage->extract($destinationHandle);
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
	$this->$iteratorCurrentPosition = 0;
    }

    function current()
    {

    }

    function key()
    {
	return $this->$iteratorCurrentPosition;
    }

    function next()
    {
	++$this->$iteratorCurrentPosition;
    }

    function valid()
    {

    }

}
