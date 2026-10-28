<?php

require __DIR__ . "/bootstrap.inc.php";

krever_autentikasjon();

$grupper_rader = database_hent_alle_grupper_med_medlemmer($pdo);

$alle_grupper = [];

foreach ($grupper_rader as $rad) {
    $gruppe_id = (int) $rad["gruppe_id"];

    if (!isset($alle_grupper[$gruppe_id])) {
        $alle_grupper[$gruppe_id] = [
            "id" => $gruppe_id,
            "navn" => $rad["navn"],
            "beskrivelse" => $rad["beskrivelse"],
            "opprettet_av" => $rad["opprettet_av"],
            "medlemmer" => [],
            "er_medlem" => false
        ];
    }

    if ($rad["student_id"] !== null) {
        $alle_grupper[$gruppe_id]["medlemmer"][] = [
            "student_id" => $rad["student_id"],
            "fornavn" => $rad["fornavn"],
            "etternavn" => $rad["etternavn"],
            "avatar_link" => $rad["avatar_link"]
        ];

        if ((int) $rad["student_id"] === (int) $_SESSION["student_id"]) {
            $alle_grupper[$gruppe_id]["er_medlem"] = true;
        }
    }
}

$alle_grupper = array_values($alle_grupper);

$side_tittel = "Hovedside";
$side_innhold = __DIR__ . "/../pages/index.tpl.php";
$side_stiler = [url("/assets/css/index.css")];

$tilstand = [
    "alle_grupper" => $alle_grupper
];

include __DIR__ . "/layout.inc.php";
