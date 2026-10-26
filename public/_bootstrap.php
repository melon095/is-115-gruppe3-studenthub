<?php

session_start();

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../components/database-funksjoner.php';
require_once __DIR__ . '/../components/csrf.php';
require_once __DIR__ . '/../components/hjelpefunksjoner.php';
require_once __DIR__ . '/../components/epost.php';

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
