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

$page_title = "Bli med i gruppe";
$page_content = __DIR__."/../pages/inviter-til-gruppe.tpl.php";
$page_styles = ["/assets/css/inviter-til-gruppe.css"];

$state = [
    "gruppe" => $gruppe,
];

include __DIR__."/_layout.php";
