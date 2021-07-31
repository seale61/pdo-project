<?php
    require_once('./vendor/autoload.php');
    require_once('./classes/pdo.class.php');

    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();

    $db = new pdoData();

    if($db->connect()) {
        echo "Hot diggity damn!\n";
        $db->close();
    }
    
    if($db->error()) {
        echo $db->error();
    }

