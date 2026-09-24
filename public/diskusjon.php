<?php

require __DIR__ . "/_bootstrap.php";

$get = filter_input_array(INPUT_GET, [
    "gruppe_id" => FILTER_VALIDATE_INT,
    "diskusjon_id" => FILTER_VALIDATE_INT
]);

$gruppe_id = $get["gruppe_id"] ?? null;
$diskusjon_id = $get["diskusjon_id"] ?? null;

if (!$gruppe_id || !$diskusjon_id) {
    header("Location: " . url("/index.php"));
    exit();
}

/*
|--------------------------------------------------------------------------
| Kontroller medlemskap
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT 1
    FROM gruppe_medlemmer
    WHERE gruppe_id = :gruppe_id
    AND bruker_id = :student_id
");

$stmt->execute([
    "gruppe_id" => $gruppe_id,
    "student_id" => $_SESSION["student_id"]
]);

if (!$stmt->fetchColumn()) {
    header("Location: " . url("/index.php"));
    exit();
}

/*
|--------------------------------------------------------------------------
| Hent gruppe
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT id, navn
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
| Hent diskusjon
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT
        id,
        gruppe_id,
        oppgave_id,
        fil_id,
        tittel,
        opprettet_på
    FROM diskusjoner
    WHERE id = :diskusjon_id
    AND gruppe_id = :gruppe_id
");

$stmt->execute([
    "diskusjon_id" => $diskusjon_id,
    "gruppe_id" => $gruppe_id
]);

$diskusjon = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$diskusjon) {
    header(
        "Location: " .
        url(
            "/gruppe.php?gruppe_id=" .
            $gruppe_id .
            "&section=diskusjoner"
        )
    );
    exit();
}

/*
|--------------------------------------------------------------------------
| Hent eventuell oppgave
|--------------------------------------------------------------------------
*/

$oppgave = null;

if ($diskusjon["oppgave_id"] !== null) {
    $stmt = $pdo->prepare("
        SELECT
            id AS oppgave_id,
            gruppe_id,
            tittel
        FROM oppgaver
        WHERE id = :oppgave_id
        AND gruppe_id = :gruppe_id
    ");

    $stmt->execute([
        "oppgave_id" => $diskusjon["oppgave_id"],
        "gruppe_id" => $gruppe_id
    ]);

    $oppgave = $stmt->fetch(PDO::FETCH_ASSOC);
}

/*
|--------------------------------------------------------------------------
| Hent eventuell fil
|--------------------------------------------------------------------------
*/

$fil = null;

if ($diskusjon["fil_id"] !== null) {
    $stmt = $pdo->prepare("
        SELECT
            id AS fil_id,
            gruppe_id,
            fil_navn
        FROM filer
        WHERE id = :fil_id
        AND gruppe_id = :gruppe_id
    ");

    $stmt->execute([
        "fil_id" => $diskusjon["fil_id"],
        "gruppe_id" => $gruppe_id
    ]);

    $fil = $stmt->fetch(PDO::FETCH_ASSOC);
}

/*
|--------------------------------------------------------------------------
| Opprett innlegg
|--------------------------------------------------------------------------
*/

$feil = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $input = filter_input_array(INPUT_POST, [
        "tekst" => FILTER_DEFAULT
    ]);

    $tekst = trim($input["tekst"] ?? "");

    if ($tekst === "") {
        $feil[] = "Innlegget kan ikke være tomt.";
    }

    if (empty($feil)) {
        $stmt = $pdo->prepare("
            INSERT INTO diskusjon_innlegg (
                diskusjon_id,
                opprettet_av,
                innhold
            )
            VALUES (
                :diskusjon_id,
                :opprettet_av,
                :innhold
            )
        ");

        $stmt->execute([
            "diskusjon_id" => $diskusjon_id,
            "opprettet_av" => $_SESSION["student_id"],
            "innhold" => $tekst
        ]);

        header(
            "Location: " .
            url(
                "/diskusjon.php?gruppe_id=" .
                $gruppe_id .
                "&diskusjon_id=" .
                $diskusjon_id
            )
        );

        exit();
    }
}

/*
|--------------------------------------------------------------------------
| Hent faktiske innlegg
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT
        i.id AS innlegg_id,
        i.innhold AS tekst,
        i.opprettet_på,
        CONCAT(s.fornavn, ' ', s.etternavn) AS forfatter_navn,
        s.avatar_link AS forfatter_avatar
    FROM diskusjon_innlegg i
    INNER JOIN studenter s
        ON s.id = i.opprettet_av
    WHERE i.diskusjon_id = :diskusjon_id
    ORDER BY i.opprettet_på ASC
");

$stmt->execute([
    "diskusjon_id" => $diskusjon_id
]);

$innlegg = $stmt->fetchAll(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| Side
|--------------------------------------------------------------------------
*/

$page_title = $diskusjon["tittel"];

$page_content = __DIR__ . "/../pages/diskusjon.tpl.php";

$page_styles = [
    url("/assets/css/diskusjon.css")
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
            $gruppe_id .
            "&section=diskusjoner"
        )
    ],
    [
        "label" => $diskusjon["tittel"]
    ]
];

$state = [
    "gruppe" => $gruppe,
    "oppgave" => $oppgave,
    "fil" => $fil,
    "diskusjon" => $diskusjon,
    "innlegg" => $innlegg
];

include __DIR__ . "/_layout.php";