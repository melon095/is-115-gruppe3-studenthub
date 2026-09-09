<?php

require __DIR__."/_bootstrap.php";

$public_page = true;

$page_title = "Logg inn";
$page_content = __DIR__."/../pages/login.tpl.php";
$page_styles = [url("/assets/css/auth.css")];

include __DIR__."/_layout.php";
