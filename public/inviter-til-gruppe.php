<?php

require __DIR__ . "/_bootstrap.php";

krever_autentikasjon();

$get = filter_input_array(INPUT_GET, [
    "gruppe_id" => FILTER_VALIDATE_INT,
    "kode" => FILTER_DEFAULT
]);

$gruppe_id = $get["gruppe_id"] ?? null;
$kode = trim($get["kode"] ?? "");

if (!$gruppe_id || $kode === "" || mb_strlen($kode) > 64) {
    omdiriger(url("/index.php"));
}

$invitasjon = database_hent_invitasjon($pdo, $gruppe_id, $kode);

if (!$invitasjon) {
    omdiriger(url("/index.php"));
}

$gruppe = [
    "id" => $invitasjon["gruppe_id"],
    "navn" => $invitasjon["navn"]
];

$feil = [];
$har_blitt_medlem = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!csrf_gyldig($_POST["csrf_token"] ?? null)) {
        $feil[] = "Skjemaet er utløpt. Prøv igjen.";
    } else {
        if (!database_er_bruker_medlem_i_gruppe($pdo, $gruppe_id, $_SESSION["student_id"])) {
            database_legg_til_gruppe_medlem($pdo, $gruppe_id, $_SESSION["student_id"]);
        }

        database_fjern_invitasjon($pdo, $invitasjon["id"]);

        $har_blitt_medlem = true;
    }
}

$side_tittel = "Bli med i gruppe";

$side_innhold = __DIR__ . "/../pages/inviter-til-gruppe.tpl.php";

$side_stiler = [
    url("/assets/css/inviter-til-gruppe.css")
];

$tilstand = [
    "gruppe" => $gruppe,
    "kode" => $kode
];

include __DIR__ . "/_layout.php";
