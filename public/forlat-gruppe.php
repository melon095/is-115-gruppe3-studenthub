<?php

require __DIR__ . "/bootstrap.inc.php";

krever_autentikasjon();

$gruppe_id = filter_input(INPUT_GET, "gruppe_id", FILTER_VALIDATE_INT)
    ?: filter_input(INPUT_POST, "gruppe_id", FILTER_VALIDATE_INT);

if (!$gruppe_id) {
    omdiriger(url("/index.php"));
}

$gruppe = database_hent_gruppe($pdo, $gruppe_id);

if (!$gruppe) {
    omdiriger(url("/index.php"));
}

$har_forlatt = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (csrf_gyldig($_POST["csrf_token"] ?? null)) {
        database_fjern_gruppe_medlem($pdo, $gruppe_id, $_SESSION['student_id']);
        $har_forlatt = true;
    }
}

$side_tittel = "Forlat gruppe";
$side_innhold = __DIR__ . "/../pages/forlat-gruppe.tpl.php";
$side_stiler = [url("/assets/css/forlat-gruppe.css")];

$brødsmulesti = [
    ["tekst" => "Grupper", "lenke" => url("/index.php")],
    ["tekst" => $gruppe["navn"], "lenke" => url("/gruppe.php?gruppe_id=" . $gruppe["id"])],
    ["tekst" => "Forlat gruppe"],
];

$tilstand = [
    "har_forlatt" => $har_forlatt,
    "gruppe" => $gruppe,
];

include __DIR__ . "/layout.inc.php";
