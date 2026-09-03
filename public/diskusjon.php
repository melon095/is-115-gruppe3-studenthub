<?php

session_start();

$gruppe_id = $_GET["gruppe_id"] ?? null;
$oppgave_id = $_GET["oppgave_id"] ?? null;

if ($gruppe_id == null) {
    header("Location: /index.php");
    exit();
}

$gruppe = [
    "id" => $gruppe_id,
    "navn" => "Gruppe " . $gruppe_id,
];

$oppgave = null;
if ($oppgave_id !== null) {
    $oppgave = [
        "oppgave_id" => $oppgave_id,
        "gruppe_id" => $gruppe_id,
        "tittel" => "Oppgave " . $oppgave_id,
    ];
}

$mock_forfattere = [
    ["navn" => "Kai Eide", "avatar_link" => "http://dummyimage.com/64x64.png/dddddd/000000"],
    ["navn" => "Mia Solberg", "avatar_link" => "http://dummyimage.com/64x64.png/dddddd/000000"],
    ["navn" => "Noah Haugen", "avatar_link" => "http://dummyimage.com/64x64.png/dddddd/000000"],
];

$mock_tekster = [
    "Har begynt på denne nå, legger ut fremgang her etter hvert.",
    "Bra jobba! Jeg kan ta en titt på det du har lastet opp i morgen.",
    "Husk å sjekke fristen, den er nærmere enn vi tror.",
];

$reaksjon_typer = [
    1 => ["emoji" => "👍", "reaksjon_navn" => "Liker"],
    2 => ["emoji" => "❤️", "reaksjon_navn" => "Elsker"],
    3 => ["emoji" => "😂", "reaksjon_navn" => "Morsomt"],
    4 => ["emoji" => "🎉", "reaksjon_navn" => "Feirer"],
    5 => ["emoji" => "😮", "reaksjon_navn" => "Overrasket"],
];

$mock_reaksjoner_per_innlegg = [
    [1 => 3, 3 => 1],
    [2 => 2],
    [],
];

$innlegg = [];
foreach ($mock_tekster as $i => $tekst) {
    $forfatter = $mock_forfattere[$i % count($mock_forfattere)];

    $reaksjoner = [];
    foreach ($mock_reaksjoner_per_innlegg[$i] as $reaksjon_type_id => $antall) {
        $reaksjoner[] = [
            "reaksjon_type_id" => $reaksjon_type_id,
            "emoji" => $reaksjon_typer[$reaksjon_type_id]["emoji"],
            "navn" => $reaksjon_typer[$reaksjon_type_id]["reaksjon_navn"],
            "antall" => $antall,
        ];
    }

    $innlegg[] = [
        "innlegg_id" => $i + 1,
        "forfatter_navn" => $forfatter["navn"],
        "forfatter_avatar" => $forfatter["avatar_link"],
        "tekst" => $tekst,
        "opprettet_på" => date("Y-m-d H:i", strtotime("-" . ((count($mock_tekster) - $i) * 6) . " hours")),
        "reaksjoner" => $reaksjoner,
    ];
}

$page_title = "Diskusjon";
$page_content = __DIR__."/../pages/diskusjon.tpl.php";
$page_styles = ["/assets/css/diskusjon.css"];

$breadcrumbs = [
    ["label" => "Grupper", "href" => "/index.php"],
    ["label" => $gruppe["navn"], "href" => "/gruppe.php?gruppe_id=" . $gruppe_id],
];

if ($oppgave !== null) {
    $breadcrumbs[] = [
        "label" => $oppgave["tittel"],
        "href" => "/oppgave.php?gruppe_id=" . $gruppe_id . "&oppgave_id=" . $oppgave["oppgave_id"],
    ];
}

$breadcrumbs[] = ["label" => "Diskusjon"];

$state = [
    "gruppe" => $gruppe,
    "oppgave" => $oppgave,
    "innlegg" => $innlegg,
];

include __DIR__."/_layout.php";
