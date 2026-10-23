<?php

require __DIR__."/_bootstrap.php";

krever_autentikasjon();

$side_tittel = "Hovedside";
$side_innhold = __DIR__."/../pages/index.tpl.php";
$side_stiler = [url("/assets/css/index.css")];

include __DIR__."/_layout.php";
