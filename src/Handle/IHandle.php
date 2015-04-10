<?php

namespace PhpEtl\Handle;

/**
 * Description of IHandle
 *
 * @author Ryan Cook <ryan@ryancook.software>
 */
interface IHandle
{

    public function extract(IHandle $destinationConnection, $sql);

    public function load(array $rows);

    public function beginTransaction();

    public function commit();

}
