<?php

session_start();

require __DIR__."/../components/csrf.php";

$gruppe_id = $_GET["gruppe_id"] ?? $_POST["gruppe_id"] ?? null;

if ($gruppe_id === null) {
    header("Location: /index.php");
    exit();
}

$gruppe = [
    "id" => $gruppe_id,
    "navn" => "Gruppe " . $gruppe_id,
];

$page_title = "Inviter medlem";
$page_content = __DIR__."/../pages/inviter.tpl.php";
$page_styles = ["/assets/css/inviter.css"];
$page_scripts = ["/assets/js/kopier-lenke.js"];

$breadcrumbs = [
    ["label" => "Grupper", "href" => "/index.php"],
    ["label" => $gruppe["navn"], "href" => "/gruppe.php?gruppe_id=" . $gruppe["id"] . "&section=medlemmer"],
    ["label" => "Inviter medlem"],
];

$state = [
    "gruppe" => $gruppe,
];

include __DIR__."/_layout.php";
