<?php

require __DIR__ . "/_bootstrap.php";

$input = filter_input_array(INPUT_GET, [
    "ressurs_id" => FILTER_VALIDATE_INT
]);

$ressurs_id = $input["ressurs_id"] ?? null;

if (!$ressurs_id) {
    header("Location: " . url("/index.php"));
    exit();
}

$stmt = $pdo->prepare("
    SELECT
        f.id AS fil_id,
        f.gruppe_id,
        f.oppgave_id,
        f.fil_navn,
        f.fil_type,
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

$gruppe_id = $ressurs["gruppe_id"];

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

if (
    $_SERVER["REQUEST_METHOD"] === "POST" &&
    isset($_FILES["ny_fil"]) &&
    $_FILES["ny_fil"]["error"] === UPLOAD_ERR_OK
) {
    $ny_fil_navn = basename($_FILES["ny_fil"]["name"]);
    $fil_storrelse = (int) $_FILES["ny_fil"]["size"];

    $stmt = $pdo->prepare("
        SELECT COALESCE(MAX(versjon_nummer), 0) + 1
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

    $lagret_navn = uniqid("", true) . "_" . $ny_fil_navn;
    $fil_lokasjon = $opplastingsmappe . $lagret_navn;

    if (move_uploaded_file($_FILES["ny_fil"]["tmp_name"], $fil_lokasjon)) {
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
                :fil_lokasjon_hdd,
                :fil_storrelse
            )
        ");

        $stmt->execute([
            "fil_id" => $ressurs_id,
            "opprettet_av" => $_SESSION["student_id"],
            "versjon_nummer" => $nytt_versjon_nummer,
            "fil_lokasjon_hdd" => $fil_lokasjon,
            "fil_storrelse" => $fil_storrelse
        ]);
    }
}

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

$versjoner = $stmt->fetchAll(PDO::FETCH_ASSOC);

$ressurs["versjoner"] = $versjoner;

if (!empty($versjoner)) {
    $ressurs["siste_versjon"] = end($versjoner);
} else {
    $ressurs["siste_versjon"] = null;
}

$page_title = "Ressurs";
$page_content = __DIR__ . "/../pages/ressurs.tpl.php";

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
        "href" => url("/gruppe.php?gruppe_id=" . $gruppe_id)
    ],
];

if ($oppgave !== null) {
    $breadcrumbs[] = [
        "label" => $oppgave["tittel"],
        "href" => url(
            "/oppgave.php?gruppe_id=" .
            $gruppe_id .
            "&oppgave_id=" .
            $oppgave["oppgave_id"]
        ),
    ];
}

$breadcrumbs[] = [
    "label" => "Ressurs " . $ressurs_id
];

$state = [
    "ressurs" => $ressurs,
    "oppgave" => $oppgave,
];

include __DIR__ . "/_layout.php";