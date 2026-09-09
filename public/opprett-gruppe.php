<?php

session_start();

$page_title = "Opprett gruppe";
$page_content = __DIR__."/../pages/opprett-gruppe.tpl.php";
$page_styles = ["/assets/css/opprett-gruppe.css"];

$breadcrumbs = [
    ["label" => "Grupper", "href" => "/index.php"],
    ["label" => "Opprett gruppe"],
];

include __DIR__."/_layout.php";
