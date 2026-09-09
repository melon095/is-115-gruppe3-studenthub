<?php

session_start();

$scriptDir = str_replace("\\", "/", dirname($_SERVER["SCRIPT_NAME"]));
if (substr($scriptDir, -7) === "/public") {
    $scriptDir = substr($scriptDir, 0, -7);
}
if ($scriptDir === "/") {
    $scriptDir = "";
}

define("BASE_URL", $scriptDir);

function url(string $path): string {
    return BASE_URL . $path;
}
