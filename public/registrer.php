<?php

require __DIR__ . "/_bootstrap.php";

$offentlig_side = true;

$feil = [];
$fornavn = "";
$etternavn = "";
$epost = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $input = filter_input_array(INPUT_POST, [
        "fornavn" => FILTER_DEFAULT,
        "etternavn" => FILTER_DEFAULT,
        "epost" => FILTER_VALIDATE_EMAIL,
        "passord" => FILTER_UNSAFE_RAW,
        "bekreft_passord" => FILTER_UNSAFE_RAW
    ]);

    $fornavn = trim($input["fornavn"] ?? "");
    $etternavn = trim($input["etternavn"] ?? "");
    $epost = $input["epost"] ?? false;
    $passord = $input["passord"] ?? "";
    $bekreft_passord = $input["bekreft_passord"] ?? "";

    if ($fornavn === "") {
        $feil[] = "Fornavn må fylles ut.";
    } elseif (mb_strlen($fornavn) > 100) {
        $feil[] = "Fornavn kan ikke være lengre enn 100 tegn.";
    }

    if ($etternavn === "") {
        $feil[] = "Etternavn må fylles ut.";
    } elseif (mb_strlen($etternavn) > 100) {
        $feil[] = "Etternavn kan ikke være lengre enn 100 tegn.";
    }

    if (!$epost) {
        $feil[] = "Du må skrive inn en gyldig e-postadresse.";
    } elseif (mb_strlen($epost) > 255) {
        $feil[] = "E-postadressen er for lang.";
    }

    if ($passord === "") {
        $feil[] = "Passord må fylles ut.";
    } elseif (mb_strlen($passord) < 8) {
        $feil[] = "Passord må være minst 8 tegn.";
    }

    if ($passord !== $bekreft_passord) {
        $feil[] = "Passordene er ikke like.";
    }

    if (empty($feil)) {
        if (database_hent_student_ved_epost($pdo, $epost)) {
            $feil[] = "Det finnes allerede en bruker med denne e-postadressen.";
        } else {
            $passord_hash = password_hash($passord, PASSWORD_DEFAULT);

            $student_id = database_opprett_student($pdo, $fornavn, $etternavn, $epost, $passord_hash);

            session_regenerate_id(true);
            $_SESSION["student_id"] = $student_id;

            omdiriger(url("/index.php"));
        }
    }
}

$side_tittel = "Registrer";
$side_innhold = __DIR__ . "/../pages/registrer.tpl.php";
$side_stiler = [url("/assets/css/auth.css")];

include __DIR__ . "/_layout.php";
