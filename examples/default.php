<?php

// Basic example
include '../vendor/autoload.php';

use PhpEtl\Handle\Database;

define('STAGE_PATH', __DIR__ . '/../..');

$source = new Database\MysqlHandle([
    'type' => 'Mysql',
    'host' => 'myhost',
    'user' => 'myuser',
    'password' => 'mypass',
    'database' => 'mydb',
    'table' => 'mytable',
    'fields' => 'field1, field2, field3'
	]);

$destination = new Database\MysqlHandle([
    'type' => 'Mysql',
    'host' => 'myhost',
    'user' => 'myuser',
    'password' => 'mypass',
    'database' => 'mydb',
    'table' => 'mytable',
    'fields' => 'field1, field2, field3'
	]);

$source->extract($destination);
