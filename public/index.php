<?php

session_start();

$page_title = "Hovedside";
$page_content = __DIR__."/../pages/index.tpl.php";

include __DIR__."/_layout.php";
