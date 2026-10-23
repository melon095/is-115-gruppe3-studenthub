<?php

session_start();

$scriptDir = str_replace("\\", "/", dirname($_SERVER["SCRIPT_NAME"]));

if ($scriptDir === "/") {
    $scriptDir = "";
}

define("BASE_URL", $scriptDir);

require_once '../config.php';

function url(string $path): string
{
    return BASE_URL . $path;
}

$database_host = DATABASE_HOST;
$database_navn = DATABASE_NAVN;

$pdo = new PDO(
    "mysql:host={$database_host};dbname={$database_navn};charset=utf8mb4",
    DATABASE_BRUKER,
    DATABASE_PASSORD,
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ]
);
