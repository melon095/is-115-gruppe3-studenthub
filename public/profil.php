<?php

require __DIR__ . "/bootstrap.inc.php";

krever_autentikasjon();

$student = database_hent_student($pdo, $_SESSION["student_id"]);

$feil = [];
$suksess = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $handling = $_POST["handling"] ?? "";

    if (!csrf_gyldig($_POST["csrf_token"] ?? null)) {
        $feil[] = "Skjemaet er utløpt. Prøv igjen.";
    } elseif ($handling === "avatar") {
        if (($_FILES["avatar"]["error"] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            $feil[] = "Velg et bilde å laste opp.";
        } else {
            $nytt_avatar_link = lagre_profilbilde($_FILES["avatar"]);

            if ($nytt_avatar_link === null) {
                $feil[] = "Filen må være et gyldig bilde (JPG, PNG, GIF eller WEBP) på maks 5 MB.";
            } else {
                slett_profilbilde_fil($student["avatar_link"]);

                database_oppdater_avatar($pdo, $_SESSION["student_id"], $nytt_avatar_link);

                $student["avatar_link"] = $nytt_avatar_link;
                $suksess = "avatar";
            }
        }
    } elseif ($handling === "passord") {
        $gammelt_passord = (string) ($_POST["gammelt_passord"] ?? "");
        $nytt_passord = (string) ($_POST["nytt_passord"] ?? "");
        $bekreft_nytt_passord = (string) ($_POST["bekreft_nytt_passord"] ?? "");

        if (!password_verify($gammelt_passord, $student["passord"])) {
            $feil[] = "Nåværende passord er feil.";
        }

        if ($nytt_passord === "") {
            $feil[] = "Nytt passord må fylles ut.";
        } else {
            $feil = array_merge($feil, valider_passordstyrke($nytt_passord));
        }

        if ($nytt_passord !== $bekreft_nytt_passord) {
            $feil[] = "Passordene er ikke like.";
        }

        if (empty($feil) && passord_er_brukt_tidligere($pdo, $_SESSION["student_id"], $nytt_passord)) {
            $feil[] = "Du kan ikke bruke et passord du har brukt tidligere.";
        }

        if (empty($feil)) {
            $passord_hash = password_hash($nytt_passord, PASSWORD_DEFAULT);

            database_oppdater_passord($pdo, $_SESSION["student_id"], $passord_hash);

            session_regenerate_id(true);

            $student["passord"] = $passord_hash;
            $suksess = "passord";
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

include __DIR__ . "/layout.inc.php";
