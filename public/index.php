<?php

require __DIR__."/_bootstrap.php";

$page_title = "Hovedside";
$page_content = __DIR__."/../pages/index.tpl.php";
$page_styles = [url("/assets/css/index.css")];

include __DIR__."/_layout.php";
