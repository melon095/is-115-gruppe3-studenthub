<?php

require __DIR__ . "/_bootstrap.php";

$get = filter_input_array(INPUT_GET, [
    "ressurs_id" => FILTER_VALIDATE_INT,
    "versjon_id" => FILTER_VALIDATE_INT,
    "last_ned" => FILTER_DEFAULT
]);

$ressurs_id = $get["ressurs_id"] ?? null;

if (!$ressurs_id) {
    header("Location: " . url("/index.php"));
    exit();
}

/*
|--------------------------------------------------------------------------
| Hent ressurs
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT
        f.id AS fil_id,
        f.gruppe_id,
        f.oppgave_id,
        f.opprettet_av,
        f.fil_navn,
        f.fil_type,
        f.fil_størrelse,
        f.fil_lokasjon_hdd,
        f.opprettet_på,
        CONCAT(s.fornavn, ' ', s.etternavn) AS opprettet_av_navn
    FROM filer f
    INNER JOIN studenter s
        ON s.id = f.opprettet_av
    WHERE f.id = :ressurs_id
");

$stmt->execute([
    "ressurs_id" => $ressurs_id
]);

$ressurs = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ressurs) {
    header("Location: " . url("/index.php"));
    exit();
}

$gruppe_id = (int) $ressurs["gruppe_id"];

/*
|--------------------------------------------------------------------------
| Kontroller at brukeren er medlem
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
| Nedlasting
|--------------------------------------------------------------------------
*/

$versjon_id = $get["versjon_id"] ?? null;
$last_ned = $get["last_ned"] ?? null;

if ($versjon_id) {
    $stmt = $pdo->prepare("
        SELECT
            id,
            fil_lokasjon_hdd
        FROM filversjoner
        WHERE id = :versjon_id
        AND fil_id = :fil_id
    ");

    $stmt->execute([
        "versjon_id" => $versjon_id,
        "fil_id" => $ressurs_id
    ]);

    $nedlasting = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($nedlasting && is_file($nedlasting["fil_lokasjon_hdd"])) {
        header("Content-Type: application/octet-stream");
        header(
            'Content-Disposition: attachment; filename="' .
            basename($ressurs["fil_navn"]) .
            '"'
        );
        header(
            "Content-Length: " .
            filesize($nedlasting["fil_lokasjon_hdd"])
        );

        readfile($nedlasting["fil_lokasjon_hdd"]);
        exit();
    }
}

if ($last_ned === "original") {
    if (
        !empty($ressurs["fil_lokasjon_hdd"]) &&
        is_file($ressurs["fil_lokasjon_hdd"])
    ) {
        header("Content-Type: application/octet-stream");
        header(
            'Content-Disposition: attachment; filename="' .
            basename($ressurs["fil_navn"]) .
            '"'
        );
        header(
            "Content-Length: " .
            filesize($ressurs["fil_lokasjon_hdd"])
        );

        readfile($ressurs["fil_lokasjon_hdd"]);
        exit();
    }
}

/*
|--------------------------------------------------------------------------
| Gruppe
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
| Oppgave
|--------------------------------------------------------------------------
*/

$oppgave = null;

if ($ressurs["oppgave_id"] !== null) {
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
        "oppgave_id" => $ressurs["oppgave_id"],
        "gruppe_id" => $gruppe_id
    ]);

    $oppgave = $stmt->fetch(PDO::FETCH_ASSOC);
}

/*
|--------------------------------------------------------------------------
| Last opp ny versjon
|--------------------------------------------------------------------------
*/

if (
    $_SERVER["REQUEST_METHOD"] === "POST" &&
    isset($_FILES["ny_fil"]) &&
    $_FILES["ny_fil"]["error"] === UPLOAD_ERR_OK
) {
    $ny_fil_navn = basename($_FILES["ny_fil"]["name"]);
    $fil_storrelse = (int) $_FILES["ny_fil"]["size"];

    $stmt = $pdo->prepare("
        SELECT COALESCE(MAX(versjon_nummer), 1) + 1
        FROM filversjoner
        WHERE fil_id = :fil_id
    ");

    $stmt->execute([
        "fil_id" => $ressurs_id
    ]);

    $nytt_versjon_nummer = (int) $stmt->fetchColumn();

    $opplastingsmappe = __DIR__ . "/../uploads/";

    if (!is_dir($opplastingsmappe)) {
        mkdir($opplastingsmappe, 0755, true);
    }

    $lagret_navn =
        uniqid("", true) .
        "_" .
        $ny_fil_navn;

    $fil_lokasjon =
        $opplastingsmappe .
        $lagret_navn;

    if (
        move_uploaded_file(
            $_FILES["ny_fil"]["tmp_name"],
            $fil_lokasjon
        )
    ) {
        $stmt = $pdo->prepare("
            INSERT INTO filversjoner (
                fil_id,
                opprettet_av,
                versjon_nummer,
                fil_lokasjon_hdd,
                fil_størrelse
            )
            VALUES (
                :fil_id,
                :opprettet_av,
                :versjon_nummer,
                :fil_lokasjon,
                :fil_storrelse
            )
        ");

        $stmt->execute([
            "fil_id" => $ressurs_id,
            "opprettet_av" => $_SESSION["student_id"],
            "versjon_nummer" => $nytt_versjon_nummer,
            "fil_lokasjon" => $fil_lokasjon,
            "fil_storrelse" => $fil_storrelse
        ]);

        header(
            "Location: " .
            url(
                "/ressurs.php?ressurs_id=" .
                $ressurs_id
            )
        );

        exit();
    }
}

/*
|--------------------------------------------------------------------------
| Versjoner
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT
        fv.id AS versjon_id,
        fv.versjon_nummer,
        CONCAT(s.fornavn, ' ', s.etternavn) AS opprettet_av_navn,
        fv.opprettet_på,
        fv.fil_størrelse
    FROM filversjoner fv
    INNER JOIN studenter s
        ON s.id = fv.opprettet_av
    WHERE fv.fil_id = :fil_id
    ORDER BY fv.versjon_nummer ASC
");

$stmt->execute([
    "fil_id" => $ressurs_id
]);

$database_versjoner = $stmt->fetchAll(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| Originalfil som versjon 1
|--------------------------------------------------------------------------
*/

$versjoner = [
    [
        "versjon_id" => null,
        "versjon_nummer" => 1,
        "opprettet_av_navn" => $ressurs["opprettet_av_navn"],
        "opprettet_på" => $ressurs["opprettet_på"],
        "fil_størrelse" => (int) $ressurs["fil_størrelse"],
        "er_original" => true
    ]
];

foreach ($database_versjoner as $versjon) {
    $versjon["er_original"] = false;
    $versjoner[] = $versjon;
}

$ressurs["versjoner"] = $versjoner;
$ressurs["siste_versjon"] = end($versjoner);

/*
|--------------------------------------------------------------------------
| Side
|--------------------------------------------------------------------------
*/

$page_title = $ressurs["fil_navn"];

$page_content =
    __DIR__ .
    "/../pages/ressurs.tpl.php";

$page_styles = [
    url("/assets/css/ressurs-tabell.css"),
    url("/assets/css/ressurs.css")
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
            "&section=ressurser"
        )
    ],
    [
        "label" => $ressurs["fil_navn"]
    ]
];

$state = [
    "ressurs" => $ressurs,
    "oppgave" => $oppgave
];

include __DIR__ . "/_layout.php";