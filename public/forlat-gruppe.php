<?php

session_start();

$gruppe_id = $_GET["gruppe_id"] ?? $_POST["gruppe_id"] ?? null;

if ($gruppe_id === null) {
    header("Location: /index.php");
    exit();
}

$gruppe = [
    "id" => $gruppe_id,
    "navn" => "Gruppe " . $gruppe_id,
];

$page_title = "Forlat gruppe";
$page_content = __DIR__."/../pages/forlat-gruppe.tpl.php";
$page_styles = ["/assets/css/forlat-gruppe.css"];

$breadcrumbs = [
    ["label" => "Grupper", "href" => "/index.php"],
    ["label" => $gruppe["navn"], "href" => "/gruppe.php?gruppe_id=" . $gruppe["id"]],
    ["label" => "Forlat gruppe"],
];

$state = [
    "gruppe" => $gruppe,
];

include __DIR__."/_layout.php";
