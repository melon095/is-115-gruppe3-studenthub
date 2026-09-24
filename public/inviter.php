<?php

require __DIR__ . "/_bootstrap.php";
require __DIR__ . "/../components/csrf.php";

$gruppe_id = filter_input(
    INPUT_GET,
    "gruppe_id",
    FILTER_VALIDATE_INT
);

if (!$gruppe_id) {
    header("Location: " . url("/index.php"));
    exit();
}

$stmt = $pdo->prepare("
    SELECT id, navn, opprettet_av
    FROM grupper
    WHERE id = :gruppe_id
");

$stmt->execute([
    "gruppe_id" => $gruppe_id
]);

$gruppe = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$gruppe) {
    header("Location: " . url("/index.php"));
    exit();
}

/*
|--------------------------------------------------------------------------
| Bare gruppeeieren kan invitere
|--------------------------------------------------------------------------
*/

if ((int) $gruppe["opprettet_av"] !== (int) $_SESSION["student_id"]) {
    header(
        "Location: " .
        url(
            "/gruppe.php?gruppe_id=" .
            $gruppe_id .
            "&section=medlemmer"
        )
    );
    exit();
}

$feil = [];
$generert_lenke = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (!csrf_gyldig($_POST["csrf_token"] ?? null)) {
        $feil[] = "Skjemaet er utløpt. Prøv igjen.";
    } else {

        $kode = bin2hex(random_bytes(16));

        $stmt = $pdo->prepare("
            INSERT INTO gruppe_invitasjoner (
                gruppe_id,
                kode,
                opprettet_av
            )
            VALUES (
                :gruppe_id,
                :kode,
                :opprettet_av
            )
        ");

        $stmt->execute([
            "gruppe_id" => $gruppe_id,
            "kode" => $kode,
            "opprettet_av" => $_SESSION["student_id"]
        ]);

        $skjema =
            (!empty($_SERVER["HTTPS"])
            && $_SERVER["HTTPS"] !== "off")
            ? "https"
            : "http";

        $generert_lenke =
            $skjema .
            "://" .
            $_SERVER["HTTP_HOST"] .
            url(
                "/inviter-til-gruppe.php?gruppe_id=" .
                $gruppe_id .
                "&kode=" .
                urlencode($kode)
            );
    }
}

$page_title = "Inviter medlem";

$page_content =
    __DIR__ .
    "/../pages/inviter.tpl.php";

$page_styles = [
    url("/assets/css/inviter.css")
];

$page_scripts = [
    url("/assets/js/kopier-lenke.js")
];

$breadcrumbs = [
    [
        "label" => "Grupper",
        "href" => url("/index.php")
    ],
    [
        "label" => $gruppe["navn"],
        "href" => url(
            "/gruppe.php?gruppe_id=" .
            $gruppe["id"] .
            "&section=medlemmer"
        )
    ],
    [
        "label" => "Inviter medlem"
    ]
];

$state = [
    "gruppe" => $gruppe
];

include __DIR__ . "/_layout.php";