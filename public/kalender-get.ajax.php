<?php

define('URL', 'https://nagerholidays.com/api/v3/NextPublicHolidays/NO');

$ch = curl_init(URL);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Accept: application/json"
]);

if (curl_errno($ch)) {
    throw new Exception("kalender-get.ajax.php feil i API respons - CURL: " . curl_error($ch));
}

$response = curl_exec($ch);
$liste = json_decode($response, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    throw new Exception('kalender-get.ajax.php feil i API response - JSON: ' . json_last_error_msg());
}

$helligdager = [];

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

include __DIR__ . "/../pages/kalender.tpl.php";
