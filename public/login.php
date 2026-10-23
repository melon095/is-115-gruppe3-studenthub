<?php

require __DIR__ . "/_bootstrap.php";

$offentlig_side = true;
$feil = [];
$epost = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $input = filter_input_array(INPUT_POST, [
        "epost" => FILTER_VALIDATE_EMAIL,
        "passord" => FILTER_UNSAFE_RAW
    ]);

    $epost = $input["epost"] ?? false;
    $passord = $input["passord"] ?? "";

    if (!$epost || $passord === "") {
        $feil[] = "Ugyldig e-post eller passord.";
    } else {
        $student = database_hent_student_ved_epost($pdo, $epost);

        if ($student && password_verify($passord, $student["passord"])) {
            session_regenerate_id(true);
            $_SESSION["student_id"] = $student["id"];

            omdiriger(url("/index.php"));
        }

        $feil[] = "Feil e-post eller passord.";
    }
}

$side_tittel = "Logg inn";
$side_innhold = __DIR__ . "/../pages/login.tpl.php";
$side_stiler = [url("/assets/css/auth.css")];

include __DIR__ . "/_layout.php";
