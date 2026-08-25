<?php

session_start();

$gruppe_id = $_GET["gruppe_id"] ?? null;
$oppgave_id = $_GET["oppgave_id"] ?? null;

if ($gruppe_id == null || $oppgave_id == null) {
    header("Location: index.php");
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


$page_title = "Oppgave";
$page_content = "../pages/oppgave.tpl.php";
//$page_styles = ["/assets/css/oppgave.css"];

$breadcrumbs = [
    ["label" => "Grupper", "href" => "/index.php"],
    ["label" => $gruppe["navn"], "href" => "/gruppe.tpl.php?gruppe_id=" . $gruppe["id"]],
    ["label" => $oppgave["tittel"]],    
];

$state = [
    "gruppe" => $gruppe,
    "oppgave" => $oppgave,
];

include __DIR__."/_layout.php";
