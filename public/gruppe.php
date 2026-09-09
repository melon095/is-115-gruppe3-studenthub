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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['gruppe_navn'])) {
    // TODO: Database integrasjon
    // TODO: Rense brukerinput
    $nytt_navn = trim($_POST['gruppe_navn']);

    if ($nytt_navn !== '') {
        $gruppe['navn'] = $nytt_navn;
    }
}

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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // TODO: Database integrasjon.
    // TODO: Rense brukerinput.
    $ny_tittel = trim($_POST['oppgave_tittel'] ?? '');
    $ny_beskrivelse = trim($_POST['oppgave_beskrivelse'] ?? '');

    if ($ny_tittel !== '') {
        $oppgaver[] = [
            "oppgave_id" => count($oppgaver) + 1,
            "gruppe_id" => $gruppe_id,
            "tittel" => $ny_tittel,
            "beskrivelse" => $ny_beskrivelse !== '' ? $ny_beskrivelse : "Ingen beskrivelse.",
            "opprettet_på" => date("Y-m-d"),
        ];
    }
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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['ny_fil']) && $_FILES['ny_fil']['error'] === UPLOAD_ERR_OK) {
    // TODO: Database integrasjon og faktisk fillagring.
    $ny_fil_navn = basename($_FILES['ny_fil']['name']);

    $ressurser[] = [
        "fil_id" => count($ressurser) + 1,
        "oppgave_id" => null,
        "opprettet_av" => $_SESSION['student_id'],
        "opprettet_av_navn" => "Deg",
        "fil_navn" => $ny_fil_navn,
        "fil_størrelse" => (int) $_FILES['ny_fil']['size'],
        "fil_type" => strtolower(pathinfo($ny_fil_navn, PATHINFO_EXTENSION)),
        "opprettet_på" => date("Y-m-d H:i"),
        "siste_versjon" => ["versjon_nummer" => 1],
        "versjoner" => [],
    ];
}

$mock_diskusjoner = [
    ["tittel" => "Spørsmål om innlevering", "oppgave_id" => 1, "fil_id" => null],
    ["tittel" => "Forslag til presentasjon", "oppgave_id" => null, "fil_id" => 2],
    ["tittel" => "Generell fremdrift i gruppa", "oppgave_id" => null, "fil_id" => null],
    ["tittel" => "Feil i kildekoden?", "oppgave_id" => null, "fil_id" => 3],
];

$diskusjoner = [];
foreach ($mock_diskusjoner as $i => $trad) {
    $diskusjoner[] = [
        "id" => $i + 1,
        "tittel" => $trad["tittel"],
        "oppgave_id" => $trad["oppgave_id"],
        "fil_id" => $trad["fil_id"],
        "antall_innlegg" => ($i * 2) + 1,
        "siste_aktivitet" => date("Y-m-d H:i", strtotime("-" . (($i + 1) * 4) . " hours")),
    ];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['diskusjon_tittel'])) {
    // TODO: Database integrasjon.
    // TODO: Rense brukerinput.
    $ny_tittel = trim($_POST['diskusjon_tittel'] ?? '');
    $merket_oppgave_id = trim($_POST['diskusjon_oppgave_id'] ?? '');
    $merket_fil_id = trim($_POST['diskusjon_fil_id'] ?? '');

    if ($ny_tittel !== '') {
        $diskusjoner[] = [
            "id" => count($diskusjoner) + 1,
            "tittel" => $ny_tittel,
            "oppgave_id" => $merket_oppgave_id !== '' ? $merket_oppgave_id : null,
            "fil_id" => $merket_fil_id !== '' ? $merket_fil_id : null,
            "antall_innlegg" => 0,
            "siste_aktivitet" => date("Y-m-d H:i"),
        ];
    }
}

$filter_oppgave_id = $_GET['oppgave_id'] ?? null;
$filter_fil_id = $_GET['fil_id'] ?? null;

$filter_oppgave = null;
if ($filter_oppgave_id !== null) {
    foreach ($oppgaver as $oppgave) {
        if ($oppgave['oppgave_id'] == $filter_oppgave_id) {
            $filter_oppgave = $oppgave;
            break;
        }
    }
}

$filter_fil = null;
if ($filter_fil_id !== null) {
    foreach ($ressurser as $ressurs) {
        if ($ressurs['fil_id'] == $filter_fil_id) {
            $filter_fil = $ressurs;
            break;
        }
    }
}

$diskusjoner_visning = $diskusjoner;
if ($filter_oppgave_id !== null) {
    $diskusjoner_visning = array_filter($diskusjoner, fn($trad) => $trad['oppgave_id'] == $filter_oppgave_id);
} elseif ($filter_fil_id !== null) {
    $diskusjoner_visning = array_filter($diskusjoner, fn($trad) => $trad['fil_id'] == $filter_fil_id);
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
  "diskusjoner_visning" => $diskusjoner_visning,
  "filter_oppgave" => $filter_oppgave,
  "filter_fil" => $filter_fil,
  "section" => $section,
];

include __DIR__."/_layout.php";
