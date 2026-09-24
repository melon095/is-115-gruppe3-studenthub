<?php

require __DIR__ . "/_bootstrap.php";
require __DIR__ . "/../components/csrf.php";

$get = filter_input_array(INPUT_GET, [
    "gruppe_id" => FILTER_VALIDATE_INT,
    "kode" => FILTER_DEFAULT
]);

$gruppe_id = $get["gruppe_id"] ?? null;
$kode = trim($get["kode"] ?? "");

if (!$gruppe_id || $kode === "") {
    header("Location: " . url("/index.php"));
    exit();
}

/*
|--------------------------------------------------------------------------
| Finn invitasjon
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT
        gi.id,
        gi.gruppe_id,
        gi.kode,
        g.navn
    FROM gruppe_invitasjoner gi
    INNER JOIN grupper g
        ON g.id = gi.gruppe_id
    WHERE gi.gruppe_id = :gruppe_id
    AND gi.kode = :kode
");

$stmt->execute([
    "gruppe_id" => $gruppe_id,
    "kode" => $kode
]);

$invitasjon = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$invitasjon) {
    header("Location: " . url("/index.php"));
    exit();
}

$gruppe = [
    "id" => $invitasjon["gruppe_id"],
    "navn" => $invitasjon["navn"]
];

$feil = [];
$har_blitt_medlem = false;

/*
|--------------------------------------------------------------------------
| Godta invitasjon
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (!csrf_gyldig($_POST["csrf_token"] ?? null)) {
        $feil[] = "Skjemaet er utløpt. Prøv igjen.";
    } else {

        $stmt = $pdo->prepare("
            SELECT 1
            FROM gruppe_medlemmer
            WHERE gruppe_id = :gruppe_id
            AND bruker_id = :bruker_id
        ");
        $stmt->execute([
            "gruppe_id" => $gruppe_id,
            "bruker_id" => $_SESSION["student_id"]
        ]);

        if (!$stmt->fetchColumn()) {

            $stmt = $pdo->prepare("
                INSERT INTO gruppe_medlemmer (
                    gruppe_id,
                    bruker_id
                )
                VALUES (
                    :gruppe_id,
                    :bruker_id
                )
            ");

            $stmt->execute([
                "gruppe_id" => $gruppe_id,
                "bruker_id" => $_SESSION["student_id"]
            ]);
        }

        /*
        | Invitasjonen brukes bare én gang
        */

        $stmt = $pdo->prepare("DELETE FROM gruppe_invitasjoner
            WHERE id = :id
        ");

        $stmt->execute([
            "id" => $invitasjon["id"]
        ]);

        $har_blitt_medlem = true;
    }
}

$page_title = "Bli med i gruppe";

$page_content =
    __DIR__ .
    "/../pages/inviter-til-gruppe.tpl.php";

$page_styles = [
    url("/assets/css/inviter-til-gruppe.css")
];

$state = [
    "gruppe" => $gruppe,
    "kode" => $kode
];

include __DIR__ . "/_layout.php";
