<?php

require __DIR__ . "/_bootstrap.php";

krever_autentikasjon();

$feil = [];
$opprettet_gruppe = null;
$innsendt_navn = "";
$innsendt_beskrivelse = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!csrf_gyldig($_POST["csrf_token"] ?? null)) {
        $feil[] = "Skjemaet er utløpt. Prøv igjen.";
    } else {
        $input = filter_input_array(INPUT_POST, [
            "navn" => FILTER_DEFAULT,
            "beskrivelse" => FILTER_DEFAULT
        ]);

        $innsendt_navn = trim($input["navn"] ?? "");
        $innsendt_beskrivelse = trim($input["beskrivelse"] ?? "");

        if ($innsendt_navn === "") {
            $feil[] = "Gruppen må ha et navn.";
        } elseif (mb_strlen($innsendt_navn) > 255) {
            $feil[] = "Gruppenavnet kan ikke være lengre enn 255 tegn.";
        }

        if (empty($feil)) {
            try {
                $pdo->beginTransaction();

                $gruppe_id = database_opprett_gruppe(
                    $pdo,
                    $innsendt_navn,
                    $innsendt_beskrivelse,
                    $_SESSION["student_id"]
                );

                database_legg_til_gruppe_medlem($pdo, $gruppe_id, $_SESSION["student_id"]);

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
}

$side_tittel = "Opprett gruppe";
$side_innhold = __DIR__ . "/../pages/opprett-gruppe.tpl.php";

$side_stiler = [
    url("/assets/css/opprett-gruppe.css")
];

$brødsmulesti = [
    [
        "tekst" => "Grupper",
        "lenke" => url("/index.php")
    ],
    [
        "tekst" => "Opprett gruppe"
    ]
];

include __DIR__ . "/_layout.php";
