<?php

session_start();

$gruppe_id = $_GET["gruppe_id"] ?? null;

if ($gruppe_id == null) {
    header("Location: index.php");
    exit();
}

$gruppe = [
    "id" => $gruppe_id,
    "navn" => "Gruppe " . $gruppe_id,
    "beskrivelse" => "Dette er beskrivelsen til gruppe '" . $gruppe_id . "'",
];

$oppgaver = [];
for ($i = 1; $i <= 4; $i++) {
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

$page_title = "Gruppe";
$page_content = "../pages/gruppe.php";
$page_styles = ["/assets/css/gruppe.css"];

$breadcrumbs = [
    ["label" => "Grupper", "href" => "/index.php"],
    ["label" => $gruppe["navn"]],
];

$state = [
  "gruppe" => $gruppe,
  "oppgaver" => $oppgaver,  
  "medlemmer" => $medlemmer,
];

include __DIR__."/_layout.php";
