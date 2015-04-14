<?php

// Basic example

include '../src/MysqlHandle.php';

$source = new MysqlHandle([
    'type' => 'Mysql',
    'host' => 'myhost',
    'user' => 'myuser',
    'password' => 'mypass',
    'database' => 'mydb'
	]);

$destination = new MysqlHandle([
    'type' => 'Mysql',
    'host' => 'myhost',
    'user' => 'myuser',
    'password' => 'mypass',
    'database' => 'mydb'
	]);

$source->send($destination, new View('newtable', 'SELECT * FROM mytable'));

