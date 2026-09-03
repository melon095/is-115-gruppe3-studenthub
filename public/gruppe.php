<?php

session_start();

$gruppe_id = $_GET["gruppe_id"] ?? null;
$section = $_GET["section"] ?? null;

if ($gruppe_id === null) {
    header("Location: /index.php");
    exit();
}

if ($section === null) {
    header("Location: /gruppe.php?gruppe_id=" . $gruppe_id . "&section=oppgaver");
    exit();
}

$gruppe = [
    "id" => $gruppe_id,
    "navn" => "Gruppe " . $gruppe_id,
    "beskrivelse" => "Dette er beskrivelsen til gruppe '" . $gruppe_id . "'",
];

$oppgaver = [];
for ($i = 1; $i <= 12; $i++) {
    $oppgaver[] = [
        "oppgave_id" => $i,
        "gruppe_id" => $gruppe_id,
        "tittel" => "Oppgave " . $i,
        "beskrivelse" => "Beskrivelse for oppgave " . $i . " i gruppe " . $gruppe_id,
        "opprettet_på" => date("Y-m-d", strtotime("-" . $i . " days")),
    ];
}

$mock_studenter = [
    ["fornavn" => "Kai", "etternavn" => "Eide"],
    ["fornavn" => "Mia", "etternavn" => "Solberg"],
    ["fornavn" => "Noah", "etternavn" => "Haugen"],
];

$medlemmer = [];
foreach ($mock_studenter as $i => $student) {
    $medlemmer[] = [
        "student_id" => $i + 1,
        "fornavn" => $student["fornavn"],
        "etternavn" => $student["etternavn"],
        "avatar_link" => "http://dummyimage.com/64x64.png/dddddd/000000",
    ];
}

$mock_filer = [
    ["fil_navn" => "Oppgavebeskrivelse.pdf", "fil_type" => "pdf", "fil_størrelse" => 245_000, "oppgave_id" => 1, "opprettet_av" => 1],
    ["fil_navn" => "Presentasjon.pptx", "fil_type" => "pptx", "fil_størrelse" => 1_540_000, "oppgave_id" => 2, "opprettet_av" => 2],
    ["fil_navn" => "Kildekode.zip", "fil_type" => "zip", "fil_størrelse" => 3_820_000, "oppgave_id" => null, "opprettet_av" => 3],
    ["fil_navn" => "Møtereferat.docx", "fil_type" => "docx", "fil_størrelse" => 58_000, "oppgave_id" => null, "opprettet_av" => 1],
];

$ressurser = [];
foreach ($mock_filer as $i => $fil) {
    $fil_id = $i + 1;
    $opprettet_av_id = $fil["opprettet_av"];
    $opprettet_av = $medlemmer[$opprettet_av_id - 1];

    $versjoner = [];
    $antall_versjoner = ($i % 3) + 1;
    for ($v = 1; $v <= $antall_versjoner; $v++) {
        $versjoner[] = [
            "versjon_id" => (($fil_id - 1) * 3) + $v,
            "fil_id" => $fil_id,
            "opprettet_av" => $opprettet_av_id,
            "versjon_nummer" => $v,
            "fil_lokasjon_hdd" => "/lagring/gruppe_" . $gruppe_id . "/filer/" . $fil_id . "/v" . $v . "_" . $fil["fil_navn"],
            "opprettet_på" => date("Y-m-d H:i", strtotime("-" . (($antall_versjoner - $v) * 2 + $i) . " days")),
        ];
    }

    $ressurser[] = [
        "fil_id" => $fil_id,
        "oppgave_id" => $fil["oppgave_id"],
        "opprettet_av" => $opprettet_av_id,
        "opprettet_av_navn" => $opprettet_av["fornavn"] . " " . $opprettet_av["etternavn"],
        "fil_navn" => $fil["fil_navn"],
        "fil_størrelse" => $fil["fil_størrelse"],
        "fil_type" => $fil["fil_type"],
        "opprettet_på" => $versjoner[0]["opprettet_på"],
        "siste_versjon" => end($versjoner),
        "versjoner" => $versjoner,
    ];
}

$page_title = "Gruppe";
$page_content = __DIR__."/../pages/gruppe.tpl.php";
$page_styles = ["/assets/css/gruppe.css", "/assets/css/ressurs-tabell.css"];

$breadcrumbs = [
    ["label" => "Grupper", "href" => "/index.php"],
    ["label" => $gruppe["navn"]],
];

$state = [
  "gruppe" => $gruppe,
  "oppgaver" => $oppgaver,  
  "medlemmer" => $medlemmer,
  "ressurser" => $ressurser,
  "section" => $section,
];

include __DIR__."/_layout.php";
