<?php

// Basic example
include '../vendor/autoload.php';

define('STAGE_PATH', __DIR__ . '/../..');

$table_definition = [
    'table' => 'mytable',
    'fields' => 'field1, field2, field3'
];

$base_connection = [
    'type' => 'Mysql',
    'host' => 'myhost',
    'user' => 'myuser',
    'password' => 'mypass'
];

$source_conn = array_merge($base_connection, ['database' => 'origin_db']);

$destination_conn = array_merge($base_connection, ['database' => 'destination_db']);

$table = new PhpEtl\Table($table_definition);

$table->extract($source_conn, $table_definition);

$table->load($destination_conn, $table_definition);
