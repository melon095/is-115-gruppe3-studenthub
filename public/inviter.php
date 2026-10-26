<?php

require __DIR__ . "/_bootstrap.php";

krever_autentikasjon();

$gruppe_id = filter_input(INPUT_GET, "gruppe_id", FILTER_VALIDATE_INT);

if (!$gruppe_id) {
    omdiriger(url("/index.php"));
}

$gruppe = database_hent_gruppe($pdo, $gruppe_id);

if (!$gruppe) {
    omdiriger(url("/index.php"));
}

if ((int) $gruppe["opprettet_av"] !== (int) $_SESSION["student_id"]) {
    omdiriger(url("/gruppe.php?gruppe_id=" . $gruppe_id . "&section=medlemmer"));
}

$feil = [];
$generert_lenke = null;
$sendt_til = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!csrf_gyldig($_POST["csrf_token"] ?? null)) {
        $feil[] = "Skjemaet er utløpt. Prøv igjen.";
    } else {
        $handling = filter_input(INPUT_POST, "handling", FILTER_DEFAULT) ?? "";

        $skjema = (!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off") ? "https" : "http";

        if ($handling === "send_epost") {
            $epost = filter_input(INPUT_POST, "epost", FILTER_VALIDATE_EMAIL);

            if (!$epost) {
                $feil[] = "Skriv inn en gyldig e-postadresse.";
            } else {
                $kode = bin2hex(random_bytes(16));

                database_opprett_invitasjon($pdo, $gruppe_id, $kode, $_SESSION["student_id"]);

                $lenke = $skjema . "://" . $_SERVER["HTTP_HOST"] . url(
                    "/inviter-til-gruppe.php?gruppe_id=" . $gruppe_id . "&kode=" . urlencode($kode)
                );

                $inviterer = database_hent_student($pdo, $_SESSION["student_id"]);

                if (send_invitasjon_epost($epost, $gruppe["navn"], $lenke, fullt_navn($inviterer))) {
                    $sendt_til = $epost;
                } else {
                    $feil[] = "Kunne ikke sende e-posten. Del lenken manuelt i stedet.";
                }
            }
        } elseif ($handling === "generer_lenke") {
            $kode = bin2hex(random_bytes(16));

            database_opprett_invitasjon($pdo, $gruppe_id, $kode, $_SESSION["student_id"]);

            $generert_lenke = $skjema . "://" . $_SERVER["HTTP_HOST"] . url(
                "/inviter-til-gruppe.php?gruppe_id=" . $gruppe_id . "&kode=" . urlencode($kode)
            );
        }
    }
}

$side_tittel = "Inviter medlem";

$side_innhold = __DIR__ . "/../pages/inviter.tpl.php";

$side_stiler = [
    url("/assets/css/inviter.css")
];

$side_skript = [
    url("/assets/js/kopier-lenke.js")
];

$brødsmulesti = [
    [
        "tekst" => "Grupper",
        "lenke" => url("/index.php")
    ],
    [
        "tekst" => $gruppe["navn"],
        "lenke" => url("/gruppe.php?gruppe_id=" . $gruppe["id"] . "&section=medlemmer")
    ],
    [
        "tekst" => "Inviter medlem"
    ]
];

$tilstand = [
    "gruppe" => $gruppe,
    "sendt_til" => $sendt_til
];

include __DIR__ . "/_layout.php";
