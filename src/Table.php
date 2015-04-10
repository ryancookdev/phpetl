<?php

namespace PhpEtl;

class Table implements \Iterator
{

    protected $stage;
    protected $iteratorCurrentPosition = 0;

    public function __construct(array $data)
    {
	$this->stage = Handle\HandleFactory::createHandle();
	$this->stage->createTable($data['table'], $data['fields']);
    }

    // Move data to the stage
    public function extract(array $sourceConnection, array $sourceData)
    {
	$source = Handle\HandleFactory::createHandle($sourceConnection, $sourceData);

	if (key_exists('sql', $sourceData)) {
	    $source->extract($this->stage, $sourceData['sql']);
	} else {
	    $source->extract($this->stage);
	}
    }

    // Move data from the stage
    public function load(array $destinationConnection, array $destinationData)
    {
	$destination = Handle\HandleFactory::createHandle($destinationConnection, $destinationData);
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
