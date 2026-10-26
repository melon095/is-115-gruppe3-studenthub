<?php

$scriptDir = str_replace("\\", "/", dirname($_SERVER["SCRIPT_NAME"]));

if ($scriptDir === "/") {
    $scriptDir = "";
}

define("BASE_URL", $scriptDir);

function url(string $path): string
{
    return BASE_URL . $path;
}

function omdiriger(string $url): never
{
    header("Location: " . $url);
    exit();
}

function krever_autentikasjon(): void
{
    if (!isset($_SESSION['student_id'])) {
        omdiriger(url("/login.php"));
    }
}

function forbokstav(string $navn): string
{
    return mb_strtoupper(mb_substr($navn, 0, 1));
}

function bytes_til_menneske(int $bytes): string
{
    if ($bytes <= 0) {
        return "0.00 B";
    }

    $enheter = ["B", "KB", "MB", "GB", "TB", "PB"];

    $eksponent = (int) floor(log($bytes, 1024));
    $eksponent = min($eksponent, count($enheter) - 1);

    return round($bytes / pow(1024, $eksponent), 2) . " " . $enheter[$eksponent];
}

function lagre_opplastet_fil(array $fil): ?array
{
    if (($fil["error"] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        return null;
    }

    if (!is_uploaded_file($fil["tmp_name"])) {
        return null;
    }

    $fil_navn = mb_substr(basename($fil["name"]), 0, 255);
    $fil_type = mb_substr(strtolower(pathinfo($fil_navn, PATHINFO_EXTENSION)), 0, 50);
    $fil_størrelse = (int) $fil["size"];

    $opplastingsmappe = __DIR__ . "/../uploads/";

    if (!is_dir($opplastingsmappe) && !mkdir($opplastingsmappe, 0755, true) && !is_dir($opplastingsmappe)) {
        return null;
    }

    $lagret_navn = bin2hex(random_bytes(16)) . "_" . $fil_navn;
    $fil_lokasjon = $opplastingsmappe . $lagret_navn;

    if (!move_uploaded_file($fil["tmp_name"], $fil_lokasjon)) {
        return null;
    }

    return [
        "fil_navn" => $fil_navn,
        "fil_type" => $fil_type,
        "fil_størrelse" => $fil_størrelse,
        "fil_lokasjon" => $fil_lokasjon
    ];
}
