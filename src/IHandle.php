<?php

require_once 'View.php';

/**
 * Description of IHandle
 *
 * @author Ryan Cook <ryan@ryancook.software>
 */
interface IHandle
{

    public function send(IHandle $destinationConnection, View $view);

    public function load(array $rows);

    public function beginTransaction();

    public function commitTransaction();

    public function defineTable($name, array $structure);

}
