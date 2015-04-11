<?php

namespace PhpEtl;

/**
 * Description of IStage
 *
 * @author Ryan Cook <ryan@ryancook.software>
 */
interface IStage
{

    public function define(array $structure);

    public function getRow();

    public function setRow();

    public function select();

    public function setTable();

    public function setTableName($name);

    public function splitColumn();

}
