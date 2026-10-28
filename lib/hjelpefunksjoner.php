<?php

$scriptDir = str_replace("\\", "/", dirname($_SERVER["SCRIPT_NAME"]));

if ($scriptDir === "/") {
    $scriptDir = "";
}

define("BASE_URL", $scriptDir);

/**
 * Bygger en fullstendig URL ved å legge applikasjonens grunnsti foran en sti.
 *
 * MÅ brukes rundt alle URL-er som vises til brukeren, siden
 * prosjektet ikke alltid ligger i roten av webserveren (f.eks. i en undermappe i XAMPP sin htdocs).
 *
 * @return string Den fullstendige URL-en.
 */
function url(string $path): string
{
    return BASE_URL . $path;
}

/**
 * Sender en HTTP-omdirigering til en annen URL og avslutter kjøringen.
 */
function omdiriger(string $url): never
{
    header("Location: " . $url);
    exit();
}

/**
 * Sender en fil som nedlasting ved å sette riktige HTTP-headere og avslutte kjøringen.
 *
 * @param string $fil_lokasjon Filens plassering på disken.
 * @param string $fil_navn Filnavnet som skal vises til brukeren som laster ned.
 */
function send_filnedlasting(string $fil_lokasjon, string $fil_navn): never
{
    header("Content-Type: application/octet-stream");
    header('Content-Disposition: attachment; filename="' . basename($fil_navn) . '"');
    header("Content-Length: " . filesize($fil_lokasjon));

    readfile($fil_lokasjon);
    exit();
}

/**
 * Sjekker at brukeren er logget inn, og omdirigerer til innloggingssiden hvis ikke.
 */
function krever_autentikasjon(): void
{
    if (!isset($_SESSION['student_id'])) {
        omdiriger(url("/login.php"));
    }
}

/**
 * Henter den første bokstaven i et navn, i stor bokstav.
 *
 * @return string Forbokstaven.
 */
function forbokstav(string $navn): string
{
    return mb_strtoupper(mb_substr($navn, 0, 1));
}

/**
 * Slår sammen fornavn og etternavn til et fullt navn.
 *
 * @param array $person En databaserad som har med "fornavn" og "etternavn".
 * @return string Det fulle navnet.
 */
function fullt_navn(array $person): string
{
    return $person["fornavn"] . " " . $person["etternavn"];
}

/**
 * Gjør om en filstørrelse i bytes til en lesbar streng, for eksempel "1.50 MB".
 *
 * @return string Den formaterte filstørrelsen.
 */
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

/**
 * Validerer og lagrer en opplastet fil i den private opplastingsmappen.
 *
 * @param array $fil Et element fra $_FILES.
 * @return ?array Informasjon om den lagrede filen, eller null hvis opplastingen feilet.
 */
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

/**
 * Validerer at en opplastet fil faktisk er et bilde, og lagrer det i den offentlige mappen for profilbilder.
 *
 * @param array $fil Et element fra $_FILES.
 * @return ?string Den offentlige URL-en til det lagrede bildet, eller null hvis opplastingen feilet eller filen ikke var et gyldig bilde.
 */
function lagre_profilbilde(array $fil): ?string
{
    $tillatte_bildetyper = [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF, IMAGETYPE_WEBP];
    $maks_størrelse = 5 * 1024 * 1024;

    if (($fil["error"] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        return null;
    }

    if (!is_uploaded_file($fil["tmp_name"])) {
        return null;
    }

    if ((int) $fil["size"] <= 0 || (int) $fil["size"] > $maks_størrelse) {
        return null;
    }

    $bildeinfo = getimagesize($fil["tmp_name"]);

    if ($bildeinfo === false || !in_array($bildeinfo[2], $tillatte_bildetyper, true)) {
        return null;
    }

    $filtype = image_type_to_extension($bildeinfo[2], false);

    $opplastingsmappe = __DIR__ . "/../public/uploads/avatarer/";

    if (!is_dir($opplastingsmappe) && !mkdir($opplastingsmappe, 0755, true) && !is_dir($opplastingsmappe)) {
        return null;
    }

    $lagret_navn = bin2hex(random_bytes(16)) . "." . $filtype;
    $fil_lokasjon = $opplastingsmappe . $lagret_navn;

    if (!move_uploaded_file($fil["tmp_name"], $fil_lokasjon)) {
        return null;
    }

    return url("/uploads/avatarer/" . $lagret_navn);
}

/**
 * Sletter profilbildefilen som hører til en gitt avatar-lenke, hvis den finnes.
 */
function slett_profilbilde_fil(?string $avatar_link): void
{
    if (empty($avatar_link)) {
        return;
    }

    $forventet_prefiks = url("/uploads/avatarer/");

    if (!str_starts_with($avatar_link, $forventet_prefiks)) {
        return;
    }

    $fil_lokasjon = __DIR__ . "/../public/uploads/avatarer/" . basename($avatar_link);

    if (is_file($fil_lokasjon)) {
        unlink($fil_lokasjon);
    }
}

/**
 * Lager HTML-en som viser profilbildet til en person, eller forbokstaven som plassholder hvis personen ikke har noe bilde.
 *
 * @param array $person En databaserad som har med "fornavn", "etternavn" og "avatar_link".
 * @param string $img_klasse CSS-klassen som skal brukes på <img>-taggen.
 * @param string $plassholder_klasse CSS-klassen som skal legges til på plassholderen når personen ikke har noe bilde.
 * @return string HTML-koden for profilbildet.
 */
function profilbilde_html(array $person, string $img_klasse, string $plassholder_klasse): string
{
    $navn = htmlspecialchars(fullt_navn($person));

    if (!empty($person["avatar_link"])) {
        return sprintf(
            '<img class="%s" src="%s" alt="%s" title="%s">',
            htmlspecialchars($img_klasse),
            htmlspecialchars($person["avatar_link"]),
            $navn,
            $navn
        );
    }

    return sprintf(
        '<span class="%s %s" aria-hidden="true" title="%s">%s</span>',
        htmlspecialchars($img_klasse),
        htmlspecialchars($plassholder_klasse),
        $navn,
        htmlspecialchars(forbokstav($person["fornavn"]))
    );
}
