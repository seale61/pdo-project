<?php
    require_once('./vendor/autoload.php');
    require_once('./classes/pdo.class.php');


    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();

    $query = new pdoData();

    if($query->connect()) {
        echo 'Hot diggity damn!';
    }
    
    if($query->error()) {
        echo $query->error();
    }

