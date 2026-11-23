<?php

include __DIR__ . "/bootstrap.inc.php";

define('URL', 'https://nagerholidays.com/api/v3/NextPublicHolidays/NO');
define('MAL', __DIR__ . '/../pages/kalender.tpl.php');

$kommende = [];
$helligdager = [];
$ch = curl_init(URL);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Accept: application/json"
]);

if (curl_errno($ch)) {
    hendelser_skriv($_SESSION["student_id"], "KalenderAjax", "Feil i CURL API respons: " . curl_error($ch));
    include MAL;
    die();
}

$respons = curl_exec($ch);
$liste = json_decode($respons, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    hendelser_skriv($_SESSION["student_id"], "KalenderAjax", "Feil i JSON API respons: " . curl_error($ch));
    include MAL;
    die();
}

foreach ($liste as $raadag) {
    $dag = [
        "dato" => new DateTime($raadag["date"] ?? date('Y M d')),
        "navn" => $raadag["localName"] ?? '??',
    ];

    $helligdager[] = $dag;
}

usort($helligdager, function ($a, $b) {
    return $a["dato"] <=> $b["dato"];
});

$kommende = array_slice($helligdager, 0, 8);

include MAL;
die();
