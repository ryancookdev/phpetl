<?php

namespace PhpEtl\Handle;

/**
 * Description of IHandle
 *
 * @author Ryan Cook <ryan@ryancook.software>
 */
interface IHandle
{

    public function extract(IHandle $destinationConnection);

    public function load(array $rows);

    public function beginTransaction();

    public function commitTransaction();

    public function define(array $structure);

}
