<?php

define("HENDELSER_FIL_LOKASJON", __DIR__ . "/../log.json");

function _hendelser_lag_fil()
{
    if (!is_file(HENDELSER_FIL_LOKASJON)) {
        file_put_contents(HENDELSER_FIL_LOKASJON, '');
    }
}

function hendelser_skriv(int $student_id, string $modul, string $tekst)
{
    _hendelser_lag_fil();

    $data = json_encode(
        [
            "ts" => time(),
            "student_id" => $student_id,
            "modul" => $modul,
            "tekst" => str_replace(["\r\n", "\r", "\n"], "", $tekst)
        ],
        JSON_INVALID_UTF8_SUBSTITUTE
    );

    $data .= PHP_EOL;

    file_put_contents(HENDELSER_FIL_LOKASJON, $data, FILE_APPEND | LOCK_EX);
}

function hendelser_hent(int $antall): array
{
    _hendelser_lag_fil();

    $fil = file(HENDELSER_FIL_LOKASJON, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $sisteNlinjer = array_slice($fil, -$antall);
    $data = [];

    foreach ($sisteNlinjer as $linje) {
        try {
            $matrise = json_decode($linje, true);

            if ($matrise === false || $matrise === null) {
                continue;
            }

            $data[] = $matrise;
        } catch (ValueError) {
            continue;
        }
    }

    return $data;
}
