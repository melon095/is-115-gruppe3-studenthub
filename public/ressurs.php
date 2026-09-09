<?php

require __DIR__."/_bootstrap.php";

$ressurs_id = $_GET["ressurs_id"] ?? null;
$gruppe_id = "1"; // TODO: Gruppe ID fra database.

if ($ressurs_id === null) {
    header("Location: " . url("/index.php"));
    exit();
}

$gruppe = [
    "id" => $gruppe_id,
    "navn" => "Gruppe " . $gruppe_id,
];

// TODO: Hent ifra database. En ressurs kan enten være knyttet til en spesifikk oppgave
// eller kun til gruppen - $oppgave er null i det siste tilfellet.
$oppgave_id = ($ressurs_id % 2 !== 0) ? "1" : null;

$oppgave = null;
if ($oppgave_id !== null) {
    $oppgave = [
        "oppgave_id" => $oppgave_id,
        "gruppe_id" => $gruppe_id,
        "tittel" => "Oppgave " . $oppgave_id,
    ];
}

$versjoner = [];
$antall_versjoner = 3;
for ($v = 1; $v <= $antall_versjoner; $v++) {
    $versjoner[] = [
        "versjon_id" => $v,
        "versjon_nummer" => $v,
        "opprettet_av_navn" => "Kai Eide",
        "opprettet_på" => date("Y-m-d H:i", strtotime("-" . (($antall_versjoner - $v) * 3) . " days")),
        "fil_størrelse" => 245_000 + ($v * 15_000),
    ];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['ny_fil']) && $_FILES['ny_fil']['error'] === UPLOAD_ERR_OK) {
    // TODO: Database integrasjon og faktisk fillagring.
    $versjoner[] = [
        "versjon_id" => count($versjoner) + 1,
        "versjon_nummer" => count($versjoner) + 1,
        "opprettet_av_navn" => "Deg",
        "opprettet_på" => date("Y-m-d H:i"),
        "fil_størrelse" => (int) $_FILES['ny_fil']['size'],
    ];
}

$ressurs = [
    "fil_id" => $ressurs_id,
    "gruppe_id" => $gruppe_id,
    "oppgave_id" => $oppgave_id,
    "fil_navn" => "Oppgavebeskrivelse.pdf",
    "fil_type" => "pdf",
    "opprettet_av_navn" => "Kai Eide",
    "opprettet_på" => $versjoner[0]["opprettet_på"],
    "siste_versjon" => end($versjoner),
    "versjoner" => $versjoner,
];

$page_title = "Ressurs";
$page_content = __DIR__."/../pages/ressurs.tpl.php";
$page_styles = [url("/assets/css/ressurs-tabell.css"), url("/assets/css/ressurs.css")];

$breadcrumbs = [
    ["label" => "Grupper", "href" => url("/index.php")],
    ["label" => $gruppe["navn"], "href" => url("/gruppe.php?gruppe_id=" . $gruppe_id)],
];

if ($oppgave !== null) {
    $breadcrumbs[] = [
        "label" => $oppgave["tittel"],
        "href" => url("/oppgave.php?gruppe_id=" . $gruppe_id . "&oppgave_id=" . $oppgave["oppgave_id"]),
    ];
}

$breadcrumbs[] = ["label" => "Ressurs " . $ressurs_id];

$state = [
    "ressurs" => $ressurs,
    "oppgave" => $oppgave,
];

include __DIR__."/_layout.php";