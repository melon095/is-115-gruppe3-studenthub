<?php

require __DIR__ . "/_bootstrap.php";

krever_autentikasjon();

$get = filter_input_array(INPUT_GET, [
    "ressurs_id" => FILTER_VALIDATE_INT,
    "versjon_id" => FILTER_VALIDATE_INT,
    "last_ned" => FILTER_DEFAULT
]);

$ressurs_id = $get["ressurs_id"] ?? null;

if (!$ressurs_id) {
    omdiriger(url("/index.php"));
}

$ressurs = database_hent_ressurs($pdo, $ressurs_id);

if (!$ressurs) {
    omdiriger(url("/index.php"));
}

$gruppe_id = (int) $ressurs["gruppe_id"];

if (!database_er_bruker_medlem_i_gruppe($pdo, $gruppe_id, $_SESSION["student_id"])) {
    omdiriger(url("/index.php"));
}

$versjon_id = $get["versjon_id"] ?? null;
$last_ned = $get["last_ned"] ?? null;

if ($versjon_id) {
    $nedlasting = database_hent_filversjon($pdo, $versjon_id, $ressurs_id);

    if ($nedlasting && is_file($nedlasting["fil_lokasjon_hdd"])) {
        header("Content-Type: application/octet-stream");
        header('Content-Disposition: attachment; filename="' . basename($ressurs["fil_navn"]) . '"');
        header("Content-Length: " . filesize($nedlasting["fil_lokasjon_hdd"]));

        readfile($nedlasting["fil_lokasjon_hdd"]);
        exit();
    }
}

if ($last_ned === "original") {
    if (!empty($ressurs["fil_lokasjon_hdd"]) && is_file($ressurs["fil_lokasjon_hdd"])) {
        header("Content-Type: application/octet-stream");
        header('Content-Disposition: attachment; filename="' . basename($ressurs["fil_navn"]) . '"');
        header("Content-Length: " . filesize($ressurs["fil_lokasjon_hdd"]));

        readfile($ressurs["fil_lokasjon_hdd"]);
        exit();
    }
}

$gruppe = database_hent_gruppe($pdo, $gruppe_id);

if (!$gruppe) {
    omdiriger(url("/index.php"));
}

$oppgave = null;

if ($ressurs["oppgave_id"] !== null) {
    $oppgave = database_hent_oppgave($pdo, $ressurs["oppgave_id"], $gruppe_id);
}

if (
    $_SERVER["REQUEST_METHOD"] === "POST" &&
    isset($_FILES["ny_fil"]) &&
    csrf_gyldig($_POST["csrf_token"] ?? null)
) {
    $lagret_fil = lagre_opplastet_fil($_FILES["ny_fil"]);

    if ($lagret_fil !== null) {
        $nytt_versjon_nummer = database_hent_neste_versjonsnummer($pdo, $ressurs_id);

        database_opprett_filversjon(
            $pdo,
            $ressurs_id,
            $_SESSION["student_id"],
            $nytt_versjon_nummer,
            $lagret_fil["fil_lokasjon"],
            $lagret_fil["fil_størrelse"]
        );

        omdiriger(url("/ressurs.php?ressurs_id=" . $ressurs_id));
    }
}

$database_versjoner = database_hent_filversjoner_for_fil($pdo, $ressurs_id);

$versjoner = [
    [
        "versjon_id" => null,
        "versjon_nummer" => 1,
        "opprettet_av_navn" => $ressurs["opprettet_av_navn"],
        "opprettet_på" => $ressurs["opprettet_på"],
        "fil_størrelse" => (int) $ressurs["fil_størrelse"],
        "er_original" => true
    ]
];

foreach ($database_versjoner as $versjon) {
    $versjon["er_original"] = false;
    $versjoner[] = $versjon;
}

$ressurs["versjoner"] = $versjoner;
$ressurs["siste_versjon"] = end($versjoner);

$side_tittel = $ressurs["fil_navn"];

$side_innhold = __DIR__ . "/../pages/ressurs.tpl.php";

$side_stiler = [
    url("/assets/css/ressurs-tabell.css"),
    url("/assets/css/ressurs.css")
];

$brødsmulesti = [
    [
        "tekst" => "Grupper",
        "lenke" => url("/index.php")
    ],
    [
        "tekst" => $gruppe["navn"],
        "lenke" => url("/gruppe.php?gruppe_id=" . $gruppe_id . "&section=ressurser")
    ],
    [
        "tekst" => $ressurs["fil_navn"]
    ]
];

$tilstand = [
    "ressurs" => $ressurs,
    "oppgave" => $oppgave
];

include __DIR__ . "/_layout.php";
