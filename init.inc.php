<?php

ini_set("display_errors", "On");
error_reporting(E_ALL ^ E_NOTICE ^ E_STRICT);
// init: 要自動載入 Pix Framework
include(__DIR__ . '/extsrc/pix/Pix/Loader.php');
set_include_path(
    __DIR__ . '/extsrc/pix/'
    . PATH_SEPARATOR . __DIR__ . '/models'
);
Pix_Loader::registerAutoload();

if (file_exists(__DIR__ . '/config/database.json')) {
    $json = file_get_contents(__DIR__ . '/config/database.json');
} else {
    echo "Can't find database.json";
    exit;
}

$config = json_decode($json)->develop;

$link = new mysqli;
$link->connect($config->host, $config->user, $config->password);
$link->select_db($config->database);
Pix_Table::setDefaultDb(new Pix_Table_Db_Adapter_Mysqli($link));
