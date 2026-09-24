<?php

session_start();

$scriptDir = str_replace("\\", "/", dirname($_SERVER["SCRIPT_NAME"]));

if (mb_substr($scriptDir, -7) === "/public") {
    $scriptDir = mb_substr($scriptDir, 0, -7);
}

if ($scriptDir === "/") {
    $scriptDir = "";
}

define("BASE_URL", $scriptDir);

function url(string $path): string {
    return BASE_URL . $path;
}


/*
|--------------------------------------------------------------------------
| Database
|--------------------------------------------------------------------------
*/

$database_host = "127.0.0.1";
$database_navn = "studenthub";
$database_bruker = "root";
$database_passord = "";

try {
    $pdo = new PDO(
        "mysql:host={$database_host};dbname={$database_navn};charset=utf8mb4",
        $database_bruker,
        $database_passord,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    exit("Kunne ikke koble til databasen.");
}