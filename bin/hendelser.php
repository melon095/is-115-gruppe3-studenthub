<?php

if (PHP_SAPI !== "cli") {
    exit();
}

require_once __DIR__ . '/../public/bootstrap.inc.php';

echo "Hendelser:" . PHP_EOL;

$hendelser = hendelser_hent(10);
foreach ($hendelser as $hendelse) {
    // https://stackoverflow.com/a/10040407
    $klokke = gmdate("Y-m-d\TH:i:s:\Z", $hendelse["ts"]);

    echo "\tKlokke($klokke) Modul($hendelse[modul]) StudentID($hendelse[student_id]): $hendelse[tekst]" . PHP_EOL;
}
