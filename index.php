<?php

require_once('./vendor/autoload.php');
require_once('./classes/pdo.class.php');

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$db = new pdoData();
$output = [];

$sql = "SELECT * FROM people";
$binds = [];

$fetchMode = '';
$data = $db->query($sql, $binds, $fetchMode);
array_push($output, 'All persons');
array_push($output, $data);

$sql = "SELECT * FROM people WHERE last_name = ?";
$binds = ['Seale'];

$fetchMode = '';
$data = $db->query($sql, $binds, $fetchMode);
array_push($output, 'Last name is '.$binds[0]);
array_push($output, $data);

$sql = "SELECT * FROM people WHERE last_name <> ?";
$binds = ['Seale'];

$fetchMode = '';
$data = $db->query($sql, $binds, $fetchMode);
array_push($output, 'Last name is not '.$binds[0]);
array_push($output, $data);

echo json_encode($output);