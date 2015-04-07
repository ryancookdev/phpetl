<?php

namespace PhpEtl;

/**
 * Description of IStage
 *
 * @author Ryan Cook <ryan@ryancook.software>
 */
interface IStage
{

    public function createTable($name, $header);

    public function getRow();

    public function setRow();

    public function select();

    public function setTable();

    public function splitColumn();

    public function insertInternalTable();

}
