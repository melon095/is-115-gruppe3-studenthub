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
    isset($_POST["diskusjon_tittel"]) &&
    $er_eier &&
    csrf_gyldig($_POST["csrf_token"] ?? null)
) {
    $ny_tittel = trim(filter_input(INPUT_POST, "diskusjon_tittel", FILTER_DEFAULT) ?? "");

    if ($ny_tittel !== "" && mb_strlen($ny_tittel) <= 255) {
        database_oppdater_diskusjon($pdo, $diskusjon_id, $gruppe_id, $ny_tittel);

        omdiriger(url("/diskusjon.php?gruppe_id=" . $gruppe_id . "&diskusjon_id=" . $diskusjon_id));
    }
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
    isset($_POST["innlegg_id"]) &&
    isset($_POST["reaksjon_type_id"]) &&
    csrf_gyldig($_POST["csrf_token"] ?? null)
) {
    $mal_innlegg_id = filter_input(INPUT_POST, "innlegg_id", FILTER_VALIDATE_INT);
    $reaksjon_type_id = filter_input(INPUT_POST, "reaksjon_type_id", FILTER_VALIDATE_INT);

    if (
        $mal_innlegg_id &&
        $reaksjon_type_id &&
        database_hent_innlegg($pdo, $mal_innlegg_id, $diskusjon_id) &&
        database_er_gyldig_reaksjonstype($pdo, $reaksjon_type_id)
    ) {
        if (database_har_reagert($pdo, $mal_innlegg_id, $reaksjon_type_id, $_SESSION["student_id"])) {
            database_fjern_reaksjon($pdo, $mal_innlegg_id, $reaksjon_type_id, $_SESSION["student_id"]);
        } else {
            database_legg_til_reaksjon($pdo, $mal_innlegg_id, $reaksjon_type_id, $_SESSION["student_id"]);
        }
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

$reaksjonstyper = database_hent_reaksjonstyper($pdo);

$reaksjoner = [];

foreach (database_hent_reaksjoner_for_diskusjon($pdo, $diskusjon_id) as $rad) {
    $rad_innlegg_id = (int) $rad["innlegg_id"];
    $rad_type_id = (int) $rad["reaksjon_type_id"];

    if (!isset($reaksjoner[$rad_innlegg_id][$rad_type_id])) {
        $reaksjoner[$rad_innlegg_id][$rad_type_id] = [
            "antall" => 0,
            "har_reagert" => false
        ];
    }

    $reaksjoner[$rad_innlegg_id][$rad_type_id]["antall"]++;

    if ((int) $rad["bruker_id"] === (int) $_SESSION["student_id"]) {
        $reaksjoner[$rad_innlegg_id][$rad_type_id]["har_reagert"] = true;
    }
}

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
    "reaksjonstyper" => $reaksjonstyper,
    "reaksjoner" => $reaksjoner,
    "er_eier" => $er_eier
];

include __DIR__ . "/_layout.php";
