<?php

namespace PhpEtl;

/**
 * Description of IHandle
 *
 * @author Ryan Cook <ryan@ryancook.software>
 */
interface IHandle
{

    public function send(IHandle $destinationConnection, array $table);

    public function load(array $rows);

    public function beginTransaction();

    public function commitTransaction();

    public function defineTable($name, array $structure);

}
