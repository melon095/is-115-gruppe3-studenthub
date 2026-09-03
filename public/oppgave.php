<?php

session_start();

$gruppe_id = $_GET["gruppe_id"] ?? null;
$oppgave_id = $_GET["oppgave_id"] ?? null;

if ($gruppe_id == null || $oppgave_id == null) {
    header("Location: /index.php");
    exit();
}

$gruppe = [
    "id" => $gruppe_id,
    "navn" => "Gruppe " . $gruppe_id,
    "beskrivelse" => "Dette er beskrivelsen til gruppe '" . $gruppe_id . "'",
];

$oppgave = [
    "oppgave_id" => $oppgave_id,
    "gruppe_id" => $gruppe_id,
    "tittel" => "Oppgave " . $oppgave_id,
    "beskrivelse" => "Beskrivelse for oppgave " . $oppgave_id . " i gruppe " . $gruppe_id,
    "opprettet_på" => date("Y-m-d", strtotime("-" . $oppgave_id . " days")),
];

$mock_filer = [
    ["fil_navn" => "Oppgavebeskrivelse.pdf", "fil_type" => "pdf", "fil_størrelse" => 245_000, "revisjoner" => 1],
    ["fil_navn" => "Kildekode.zip", "fil_type" => "zip", "fil_størrelse" => 1_820_000, "revisjoner" => 3],
];

$ressurser = [];
foreach ($mock_filer as $i => $fil) {
    $ressurser[] = [
        "fil_id" => $i + 1,
        "oppgave_id" => $oppgave_id,
        "fil_navn" => $fil["fil_navn"],
        "fil_type" => $fil["fil_type"],
        "fil_størrelse" => $fil["fil_størrelse"],
        "siste_versjon" => ["versjon_nummer" => $fil["revisjoner"]],
    ];
}

$page_title = "Oppgave";
$page_content = __DIR__."/../pages/oppgave.tpl.php";
$page_styles = ["/assets/css/oppgave.css", "/assets/css/ressurs-tabell.css"];

$breadcrumbs = [
    ["label" => "Grupper", "href" => "/index.php"],
    ["label" => $gruppe["navn"], "href" => "/gruppe.php?gruppe_id=" . $gruppe["id"]],
    ["label" => $oppgave["tittel"]],
];

$state = [
    "gruppe" => $gruppe,
    "oppgave" => $oppgave,
    "ressurser" => $ressurser,
];

include __DIR__."/_layout.php";
