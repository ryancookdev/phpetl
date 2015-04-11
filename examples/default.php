<?php

// Basic example
include '../vendor/autoload.php';

define('STAGE_PATH', __DIR__ . '/../..');

$tableDefinition = [
    'table' => 'mytable',
    'fields' => 'field1, field2, field3'
];

$base_connection = [
    'type' => 'Mysql',
    'host' => 'myhost',
    'user' => 'myuser',
    'password' => 'mypass'
];

$sourceConnection = array_merge($base_connection, ['database' => 'origin_db']);

$destinationConnection = array_merge($base_connection, ['database' => 'destination_db']);

$table = new PhpEtl\Table($tableDefinition);

$table->extract($sourceConnection, $tableDefinition);

$table->load($destinationConnection, $tableDefinition);
