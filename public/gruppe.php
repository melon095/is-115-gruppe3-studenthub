<?php

require __DIR__ . "/_bootstrap.php";

krever_autentikasjon();

$get = filter_input_array(INPUT_GET, [
    "gruppe_id" => FILTER_VALIDATE_INT,
    "section" => FILTER_SANITIZE_SPECIAL_CHARS
]);

$gruppe_id = $get["gruppe_id"] ?? null;
$section = $get["section"] ?? null;

$gyldige_sections = ["oppgaver", "medlemmer", "ressurser", "diskusjoner"];

if (!$gruppe_id) {
    omdiriger(url("/index.php"));
}

if (!in_array($section, $gyldige_sections, true)) {
    omdiriger(url("/gruppe.php?gruppe_id=" . $gruppe_id . "&section=oppgaver"));
}

$gruppe = database_hent_gruppe($pdo, $gruppe_id);

if (!$gruppe) {
    omdiriger(url("/index.php"));
}

if (!database_er_bruker_medlem_i_gruppe($pdo, $gruppe_id, $_SESSION['student_id'])) {
    omdiriger(url("/index.php"));
}

$er_eier = (int) $gruppe["opprettet_av"] === (int) $_SESSION["student_id"];

if (
    $_SERVER["REQUEST_METHOD"] === "POST" &&
    $er_eier === true &&
    isset($_POST["admin_handling"]) &&
    csrf_gyldig($_POST['csrf_token'] ?? null)
) {
    $input = filter_input_array(INPUT_POST, [
        "admin_handling" => FILTER_SANITIZE_SPECIAL_CHARS,
        "student_id" => FILTER_VALIDATE_INT,
        "oppgave_id" => FILTER_VALIDATE_INT,
        "fil_id" => FILTER_VALIDATE_INT,
        "diskusjon_id" => FILTER_VALIDATE_INT
    ]);

    $handling = $input["admin_handling"] ?? "";

    if ($handling === "fjern_medlem") {
        $student_id = $input["student_id"] ?? null;

        if ($student_id && (int) $student_id !== (int) $gruppe["opprettet_av"]) {
            database_fjern_gruppe_medlem($pdo, $gruppe_id, $student_id);
        }

        omdiriger(url("/gruppe.php?gruppe_id=" . $gruppe_id . "&section=medlemmer"));
    }

    if ($handling === "slett_oppgave") {
        $oppgave_id = $input["oppgave_id"] ?? null;

        if ($oppgave_id) {
            database_fjern_oppgave($pdo, $oppgave_id, $gruppe_id);
        }

        omdiriger(url("/gruppe.php?gruppe_id=" . $gruppe_id . "&section=oppgaver"));
    }

    if ($handling === "slett_diskusjon") {
        $diskusjon_id = $input["diskusjon_id"] ?? null;

        if ($diskusjon_id) {
            database_fjern_diskusjon($pdo, $diskusjon_id, $gruppe_id);
        }

        omdiriger(url("/gruppe.php?gruppe_id=" . $gruppe_id . "&section=diskusjoner"));
    }

    if ($handling === "slett_ressurs") {
        $fil_id = $input["fil_id"] ?? null;

        if ($fil_id) {
            database_fjern_fil($pdo, $fil_id, $gruppe_id);
        }

        omdiriger(url("/gruppe.php?gruppe_id=" . $gruppe_id . "&section=ressurser"));
    }
}

if (
    $_SERVER["REQUEST_METHOD"] === "POST" &&
    isset($_POST["gruppe_navn"]) &&
    $er_eier &&
    csrf_gyldig($_POST["csrf_token"] ?? null)
) {
    $nytt_navn = trim(filter_input(INPUT_POST, "gruppe_navn", FILTER_DEFAULT) ?? "");
    $ny_beskrivelse = trim(filter_input(INPUT_POST, "gruppe_beskrivelse", FILTER_DEFAULT) ?? "");

    if ($nytt_navn !== "" && mb_strlen($nytt_navn) <= 255) {
        database_oppdater_gruppe($pdo, $gruppe_id, $nytt_navn, $ny_beskrivelse);

        $gruppe["navn"] = $nytt_navn;
        $gruppe["beskrivelse"] = $ny_beskrivelse;
    }
}

if (
    $_SERVER["REQUEST_METHOD"] === "POST" &&
    isset($_POST["oppgave_tittel"]) &&
    csrf_gyldig($_POST["csrf_token"] ?? null)
) {
    $ny_tittel = trim(filter_input(INPUT_POST, "oppgave_tittel", FILTER_DEFAULT) ?? "");
    $ny_beskrivelse = trim(filter_input(INPUT_POST, "oppgave_beskrivelse", FILTER_DEFAULT) ?? "");

    if ($ny_tittel !== "" && mb_strlen($ny_tittel) <= 255) {
        database_opprett_oppgave($pdo, $gruppe_id, $ny_tittel, $ny_beskrivelse);

        omdiriger(url("/gruppe.php?gruppe_id=" . $gruppe_id . "&section=oppgaver"));
    }
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
            null,
            $_SESSION["student_id"],
            $lagret_fil["fil_navn"],
            $lagret_fil["fil_størrelse"],
            $lagret_fil["fil_type"],
            $lagret_fil["fil_lokasjon"]
        );

        omdiriger(url("/gruppe.php?gruppe_id=" . $gruppe_id . "&section=ressurser"));
    }
}

if (
    $_SERVER["REQUEST_METHOD"] === "POST" &&
    isset($_POST["diskusjon_tittel"]) &&
    csrf_gyldig($_POST["csrf_token"] ?? null)
) {
    $input = filter_input_array(INPUT_POST, [
        "diskusjon_tittel" => FILTER_DEFAULT,
        "diskusjon_oppgave_id" => FILTER_VALIDATE_INT,
        "diskusjon_fil_id" => FILTER_VALIDATE_INT
    ]);

    $ny_tittel = trim($input["diskusjon_tittel"] ?? "");
    $merket_oppgave_id = $input["diskusjon_oppgave_id"] ?? null;
    $merket_fil_id = $input["diskusjon_fil_id"] ?? null;

    if ($ny_tittel !== "" && mb_strlen($ny_tittel) <= 255) {
        database_opprett_diskusjon(
            $pdo,
            $gruppe_id,
            $ny_tittel,
            $merket_oppgave_id ?: null,
            $merket_fil_id ?: null,
            $_SESSION["student_id"]
        );

        omdiriger(url("/gruppe.php?gruppe_id=" . $gruppe_id . "&section=diskusjoner"));
    }
}

$oppgaver = database_hent_oppgaver_for_gruppe($pdo, $gruppe_id);
$medlemmer = database_hent_medlemmer_for_gruppe($pdo, $gruppe_id);
$ressurser = database_hent_ressurser_for_gruppe($pdo, $gruppe_id);

foreach ($ressurser as &$ressurs) {
    $ressurs["siste_versjon"] = database_hent_siste_filversjon($pdo, $ressurs["fil_id"]) ?: ["versjon_nummer" => 1];
}

unset($ressurs);

$oppgave_etter_id = [];

foreach ($oppgaver as $oppgave) {
    $oppgave_etter_id[(int) $oppgave["oppgave_id"]] = $oppgave;
}

$ressurs_etter_id = [];

foreach ($ressurser as $ressurs) {
    $ressurs_etter_id[(int) $ressurs["fil_id"]] = $ressurs;
}

$diskusjoner = database_hent_diskusjoner_for_gruppe($pdo, $gruppe_id);

$side_tittel = "Gruppe";

$side_innhold = __DIR__ . "/../pages/gruppe.tpl.php";

$side_stiler = [
    url("/assets/css/gruppe.css"),
    url("/assets/css/ressurs-tabell.css")
];

$brødsmulesti = [
    [
        "tekst" => "Grupper",
        "lenke" => url("/index.php")
    ],
    [
        "tekst" => $gruppe["navn"]
    ]
];

$tilstand = [
    "gruppe" => $gruppe,
    "oppgaver" => $oppgaver,
    "medlemmer" => $medlemmer,
    "ressurser" => $ressurser,
    "diskusjoner" => $diskusjoner,
    "oppgave_etter_id" => $oppgave_etter_id,
    "ressurs_etter_id" => $ressurs_etter_id,
    "section" => $section,
    "er_eier" => $er_eier
];

include __DIR__ . "/_layout.php";
