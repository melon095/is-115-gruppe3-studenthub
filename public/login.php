<?php

require __DIR__ . "/_bootstrap.php";

$public_page = true;
$feil = [];
$epost = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $input = filter_input_array(INPUT_POST, [
        "epost" => FILTER_VALIDATE_EMAIL,
        "passord" => FILTER_DEFAULT
    ]);

    $epost = $input["epost"] ?? false;
    $passord = $input["passord"] ?? "";

    if (!$epost || $passord === "") {
        $feil[] = "Ugyldig e-post eller passord.";
    } else {
        $stmt = $pdo->prepare("
            SELECT id, passord
            FROM studenter
            WHERE epost = :epost
        ");

        $stmt->execute([
            "epost" => $epost
        ]);

        $student = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($student && password_verify($passord, $student["passord"])) {
            $_SESSION["student_id"] = $student["id"];

            header("Location: " . url("/index.php"));
            exit();
        }

        $feil[] = "Feil e-post eller passord.";
    }
}

$page_title = "Logg inn";
$page_content = __DIR__ . "/../pages/login.tpl.php";
$page_styles = [url("/assets/css/auth.css")];

include __DIR__ . "/_layout.php";
