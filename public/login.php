<?php

session_start();

$public_page = true;

$page_title = "Logg inn";
$page_content = __DIR__."/../pages/login.tpl.php";
$page_styles = ["/assets/css/auth.css"];

include __DIR__."/_layout.php";
