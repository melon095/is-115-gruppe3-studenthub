<?php

require __DIR__."/_bootstrap.php";

$page_title = "Opprett gruppe";
$page_content = __DIR__."/../pages/opprett-gruppe.tpl.php";
$page_styles = [url("/assets/css/opprett-gruppe.css")];

$breadcrumbs = [
    ["label" => "Grupper", "href" => url("/index.php")],
    ["label" => "Opprett gruppe"],
];

include __DIR__."/_layout.php";
