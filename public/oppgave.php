<?php

require __DIR__ . "/_bootstrap.php";

krever_autentikasjon();

$get = filter_input_array(INPUT_GET, [
    "gruppe_id" => FILTER_VALIDATE_INT,
    "oppgave_id" => FILTER_VALIDATE_INT
]);

$gruppe_id = $get["gruppe_id"] ?? null;
$oppgave_id = $get["oppgave_id"] ?? null;

if (!$gruppe_id || !$oppgave_id) {
    omdiriger(url("/index.php"));
}

$gruppe = database_hent_gruppe($pdo, $gruppe_id);

if (!$gruppe) {
    omdiriger(url("/index.php"));
}

if (!database_er_bruker_medlem_i_gruppe($pdo, $gruppe_id, $_SESSION["student_id"])) {
    omdiriger(url("/index.php"));
}

$oppgave = database_hent_oppgave($pdo, $oppgave_id, $gruppe_id);

if (!$oppgave) {
    omdiriger(url("/gruppe.php?gruppe_id=" . $gruppe_id . "&section=oppgaver"));
}

if (
    $_SERVER["REQUEST_METHOD"] === "POST" &&
    isset($_FILES["ny_fil"]) &&
    csrf_gyldig($_POST["csrf_token"] ?? null)
) {
    $lagret_fil = lagre_opplastet_fil($_FILES["ny_fil"]);

    if ($lagret_fil !== null) {
        database_opprett_fil(
            $pdo,
            $gruppe_id,
            $oppgave_id,
            $_SESSION["student_id"],
            $lagret_fil["fil_navn"],
            $lagret_fil["fil_størrelse"],
            $lagret_fil["fil_type"],
            $lagret_fil["fil_lokasjon"]
        );

        omdiriger(url("/oppgave.php?gruppe_id=" . $gruppe_id . "&oppgave_id=" . $oppgave_id));
    }
}

$ressurser = database_hent_ressurser_for_oppgave($pdo, $gruppe_id, $oppgave_id);

foreach ($ressurser as &$ressurs) {
    $ressurs["siste_versjon"] = database_hent_siste_filversjon($pdo, $ressurs["fil_id"]) ?: ["versjon_nummer" => 1];
}

unset($ressurs);

$antall_diskusjoner = database_tell_diskusjoner_for_oppgave($pdo, $gruppe_id, $oppgave_id);

$side_tittel = "Oppgave";
$side_innhold = __DIR__ . "/../pages/oppgave.tpl.php";
$side_stiler = [
    url("/assets/css/oppgave.css"),
    url("/assets/css/ressurs-tabell.css")
];

$brødsmulesti = [
    [
        "tekst" => "Grupper",
        "lenke" => url("/index.php")
    ],
    [
        "tekst" => $gruppe["navn"],
        "lenke" => url("/gruppe.php?gruppe_id=" . $gruppe["id"])
    ],
    [
        "tekst" => $oppgave["tittel"]
    ],
];

$tilstand = [
    "gruppe" => $gruppe,
    "oppgave" => $oppgave,
    "ressurser" => $ressurser,
    "antall_diskusjoner" => $antall_diskusjoner,
];

include __DIR__ . "/_layout.php";
