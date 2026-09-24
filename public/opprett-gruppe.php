<?php

require __DIR__ . "/_bootstrap.php";

$feil = [];
$opprettet_gruppe = null;
$innsendt_navn = "";
$innsendt_beskrivelse = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $input = filter_input_array(INPUT_POST, [
        "navn" => FILTER_DEFAULT,
        "beskrivelse" => FILTER_DEFAULT
    ]);

    $innsendt_navn = trim($input["navn"] ?? "");
    $innsendt_beskrivelse = trim($input["beskrivelse"] ?? "");

    if ($innsendt_navn === "") {
        $feil[] = "Gruppen må ha et navn.";
    }

    if (empty($feil)) {
        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("
                INSERT INTO grupper (
                    navn,
                    beskrivelse,
                    opprettet_av
                )
                VALUES (
                    :navn,
                    :beskrivelse,
                    :opprettet_av
                )
            ");

            $stmt->execute([
                "navn" => $innsendt_navn,
                "beskrivelse" => $innsendt_beskrivelse,
                "opprettet_av" => $_SESSION["student_id"]
            ]);

            $gruppe_id = $pdo->lastInsertId();

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

            $pdo->commit();

            $opprettet_gruppe = [
                "id" => $gruppe_id,
                "navn" => $innsendt_navn,
                "beskrivelse" => $innsendt_beskrivelse
            ];
        } catch (PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            $feil[] = "Kunne ikke opprette gruppen.";
        }
    }
}

$page_title = "Opprett gruppe";
$page_content = __DIR__ . "/../pages/opprett-gruppe.tpl.php";

$page_styles = [
    url("/assets/css/opprett-gruppe.css")
];

$breadcrumbs = [
    [
        "label" => "Grupper",
        "href" => url("/index.php")
    ],
    [
        "label" => "Opprett gruppe"
    ]
];

include __DIR__ . "/_layout.php";