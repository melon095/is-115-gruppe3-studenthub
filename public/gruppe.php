<?php

session_start();

$gruppe_id = $_GET["gruppe_id"];

// TODO: Hvis qp id ikke finnes

$page_title = "Gruppe";
$page_content = "../pages/gruppe.tpl.php";

include __DIR__."/_layout.php";
