<?php

require __DIR__ . "/_bootstrap.php";

krever_autentikasjon();

$feil = [];

$get = filter_input_array(INPUT_GET, [
    "gruppe_id" => FILTER_VALIDATE_INT,
    "diskusjon_id" => FILTER_VALIDATE_INT
]);

$gruppe_id = $get["gruppe_id"] ?? null;
$diskusjon_id = $get["diskusjon_id"] ?? null;

if (!$gruppe_id || !$diskusjon_id) {
    omdiriger(url("/index.php"));
}

$gruppe = database_hent_gruppe($pdo, $gruppe_id);

if (!$gruppe) {
    omdiriger(url("/index.php"));
}

if (!database_er_bruker_medlem_i_gruppe($pdo, $gruppe_id, $_SESSION['student_id'])) {
    omdiriger(url("/index.php"));
}

$er_eier = (int) $gruppe["opprettet_av"] === (int) $_SESSION["student_id"];

$diskusjon = database_hent_diskusjon($pdo, $gruppe_id, $diskusjon_id);

if (!$diskusjon) {
    omdiriger(url("/gruppe.php?gruppe_id=" . $gruppe_id . "&section=diskusjoner"));
}

if (
    $_SERVER["REQUEST_METHOD"] === "POST" &&
    isset($_POST["slett_innlegg"]) &&
    $er_eier &&
    csrf_gyldig($_POST["csrf_token"] ?? null)
) {
    $innlegg_id = filter_input(INPUT_POST, "innlegg_id", FILTER_VALIDATE_INT);

    if ($innlegg_id) {
        database_fjern_diskusjon_innlegg($pdo, $diskusjon_id, $gruppe_id, $innlegg_id);
    }

    omdiriger(url("/diskusjon.php?gruppe_id=" . $gruppe_id . "&diskusjon_id=" . $diskusjon_id));
}

if (
    $_SERVER["REQUEST_METHOD"] === "POST" &&
    isset($_POST["tekst"]) &&
    csrf_gyldig($_POST["csrf_token"] ?? null)
) {
    $tekst = trim(filter_input(INPUT_POST, "tekst", FILTER_DEFAULT) ?? "");

    if ($tekst === "") {
        $feil[] = "Innlegget kan ikke være tomt.";
    } elseif (mb_strlen($tekst) > 5000) {
        $feil[] = "Innlegget kan ikke være lengre enn 5000 tegn.";
    }

    if (empty($feil)) {
        database_opprett_innlegg($pdo, $diskusjon_id, $_SESSION['student_id'], $tekst);

        omdiriger(url("/diskusjon.php?gruppe_id=" . $gruppe_id . "&diskusjon_id=" . $diskusjon_id));
    }
}

$oppgave = null;

if ($diskusjon["oppgave_id"] !== null) {
    $oppgave = database_hent_oppgave($pdo, $diskusjon['oppgave_id'], $gruppe_id);
}

$fil = null;

if ($diskusjon["fil_id"] !== null) {
    $fil = database_hent_fil($pdo, $diskusjon['fil_id'], $gruppe_id);
}

$innlegg = database_hent_innlegger_i_diskusjon($pdo, $diskusjon_id);

$side_tittel = $diskusjon["tittel"];

$side_innhold = __DIR__ . "/../pages/diskusjon.tpl.php";

$side_stiler = [
    url("/assets/css/diskusjon.css")
];

$brødsmulesti = [
    [
        "tekst" => "Grupper",
        "lenke" => url("/index.php")
    ],
    [
        "tekst" => $gruppe["navn"],
        "lenke" => url("/gruppe.php?gruppe_id=" . $gruppe_id . "&section=diskusjoner")
    ],
    [
        "tekst" => $diskusjon["tittel"]
    ]
];

$tilstand = [
    "gruppe" => $gruppe,
    "oppgave" => $oppgave,
    "fil" => $fil,
    "diskusjon" => $diskusjon,
    "innlegg" => $innlegg,
    "er_eier" => $er_eier
];

include __DIR__ . "/_layout.php";
