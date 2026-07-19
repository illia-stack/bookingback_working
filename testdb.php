<?php

ini_set('display_errors',1);
error_reporting(E_ALL);

$config = require "config/config.php";

$db = $config['db'];

try {

    $pdo = new PDO(
        "pgsql:host={$db['host']};port={$db['port']};dbname={$db['database']};sslmode=require",
        $db['username'],
        $db['password']
    );

    echo "CONNECTED";

}
catch(PDOException $e){

    echo $e->getMessage();

}