<?php

if (PHP_SAPI !== "cli") {
    exit();
}

require_once __DIR__ . "/../public/bootstrap.inc.php";

$filbane = realpath(__DIR__ . "/../studenter.csv");
$fil = fopen($filbane, 'w');

$studenter = database_hent_studenter($pdo);

// Fikse ÆØÅ https://stackoverflow.com/a/49561209
fputs($fil, (chr(0xEF) . chr(0xBB) . chr(0xBF)));

// Header
fputcsv($fil, [
    "Fornavn",
    "Etternavn",
    "Epost",
    "Mobilnummer",
    "Opprettet På"
]);

foreach ($studenter as $student) {
    fputcsv($fil, [
        $student["fornavn"],
        $student["etternavn"],
        $student["epost"],
        $student["mobilnummer"],
        $student["opprettet_på"]
    ]);
}

fclose($fil);

echo "Studenter tabellen er i filen '$filbane'" . PHP_EOL;
