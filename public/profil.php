<?php

require __DIR__ . "/_bootstrap.php";

krever_autentikasjon();

$student = database_hent_student($pdo, $_SESSION["student_id"]);

$feil = [];
$suksess = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!csrf_gyldig($_POST["csrf_token"] ?? null)) {
        $feil[] = "Skjemaet er utløpt. Prøv igjen.";
    } elseif (($_FILES["avatar"]["error"] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        $feil[] = "Velg et bilde å laste opp.";
    } else {
        $nytt_avatar_link = lagre_profilbilde($_FILES["avatar"]);

        if ($nytt_avatar_link === null) {
            $feil[] = "Filen må være et gyldig bilde (JPG, PNG, GIF eller WEBP) på maks 5 MB.";
        } else {
            slett_profilbilde_fil($student["avatar_link"]);

            database_oppdater_avatar($pdo, $_SESSION["student_id"], $nytt_avatar_link);

            $student["avatar_link"] = $nytt_avatar_link;
            $suksess = true;
        }
    }
}

$side_tittel = "Min profil";

$side_innhold = __DIR__ . "/../pages/profil.tpl.php";

$side_stiler = [
    url("/assets/css/profil.css")
];

$brødsmulesti = [
    ["tekst" => "Min profil"]
];

$tilstand = [
    "student" => $student,
    "suksess" => $suksess
];

include __DIR__ . "/_layout.php";
