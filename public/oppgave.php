<?php

require __DIR__ . "/_bootstrap.php";

$input = filter_input_array(INPUT_GET, [
    "gruppe_id" => FILTER_VALIDATE_INT,
    "oppgave_id" => FILTER_VALIDATE_INT
]);

$gruppe_id = $input["gruppe_id"] ?? null;
$oppgave_id = $input["oppgave_id"] ?? null;

if (!$gruppe_id || !$oppgave_id) {
    header("Location: " . url("/index.php"));
    exit();
}

$stmt = $pdo->prepare("
    SELECT id, navn, beskrivelse
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

$stmt = $pdo->prepare("
    SELECT
        id AS oppgave_id,
        gruppe_id,
        tittel,
        beskrivelse,
        opprettet_på
    FROM oppgaver
    WHERE id = :oppgave_id
    AND gruppe_id = :gruppe_id
");

$stmt->execute([
    "oppgave_id" => $oppgave_id,
    "gruppe_id" => $gruppe_id
]);

$oppgave = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$oppgave) {
    header("Location: " . url("/gruppe.php?gruppe_id=" . $gruppe_id . "&section=oppgaver"));
    exit();
}

if (
    $_SERVER["REQUEST_METHOD"] === "POST" &&
    isset($_FILES["ny_fil"]) &&
    $_FILES["ny_fil"]["error"] === UPLOAD_ERR_OK
) {
    $ny_fil_navn = basename($_FILES["ny_fil"]["name"]);
    $fil_type = strtolower(pathinfo($ny_fil_navn, PATHINFO_EXTENSION));
    $fil_storrelse = (int) $_FILES["ny_fil"]["size"];

    $opplastingsmappe = __DIR__ . "/../uploads/";

    if (!is_dir($opplastingsmappe)) {
        mkdir($opplastingsmappe, 0755, true);
    }

    $lagret_navn = uniqid("", true) . "_" . $ny_fil_navn;
    $fil_lokasjon = $opplastingsmappe . $lagret_navn;

    if (move_uploaded_file($_FILES["ny_fil"]["tmp_name"], $fil_lokasjon)) {
        $stmt = $pdo->prepare("
            INSERT INTO filer (
                gruppe_id,
                oppgave_id,
                opprettet_av,
                fil_navn,
                fil_størrelse,
                fil_type,
                fil_lokasjon_hdd
            )
            VALUES (
                :gruppe_id,
                :oppgave_id,
                :opprettet_av,
                :fil_navn,
                :fil_storrelse,
                :fil_type,
                :fil_lokasjon
            )
        ");

        $stmt->execute([
            "gruppe_id" => $gruppe_id,
            "oppgave_id" => $oppgave_id,
            "opprettet_av" => $_SESSION["student_id"],
            "fil_navn" => $ny_fil_navn,
            "fil_storrelse" => $fil_storrelse,
            "fil_type" => $fil_type,
            "fil_lokasjon" => $fil_lokasjon
        ]);
    }
}

$stmt = $pdo->prepare("
    SELECT
        f.id AS fil_id,
        f.oppgave_id,
        f.fil_navn,
        f.fil_type,
        f.fil_størrelse
    FROM filer f
    WHERE f.gruppe_id = :gruppe_id
    AND f.oppgave_id = :oppgave_id
    ORDER BY f.id DESC
");

$stmt->execute([
    "gruppe_id" => $gruppe_id,
    "oppgave_id" => $oppgave_id
]);

$ressurser = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($ressurser as &$ressurs) {
    $stmt = $pdo->prepare("
        SELECT versjon_nummer
        FROM filversjoner
        WHERE fil_id = :fil_id
        ORDER BY versjon_nummer DESC
        LIMIT 1
    ");

    $stmt->execute([
        "fil_id" => $ressurs["fil_id"]
    ]);

    $siste_versjon = $stmt->fetch(PDO::FETCH_ASSOC);

    $ressurs["siste_versjon"] = $siste_versjon ?: [
        "versjon_nummer" => 1
    ];
}

unset($ressurs);

$stmt = $pdo->prepare("
    SELECT COUNT(*)
    FROM diskusjoner
    WHERE gruppe_id = :gruppe_id
    AND oppgave_id = :oppgave_id
");

$stmt->execute([
    "gruppe_id" => $gruppe_id,
    "oppgave_id" => $oppgave_id
]);

$antall_diskusjoner = (int) $stmt->fetchColumn();

$page_title = "Oppgave";
$page_content = __DIR__ . "/../pages/oppgave.tpl.php";
$page_styles = [
    url("/assets/css/oppgave.css"),
    url("/assets/css/ressurs-tabell.css")
];

$breadcrumbs = [
    [
        "label" => "Grupper",
        "href" => url("/index.php")
    ],
    [
        "label" => $gruppe["navn"],
        "href" => url("/gruppe.php?gruppe_id=" . $gruppe["id"])
    ],
    [
        "label" => $oppgave["tittel"]
    ],
];

$state = [
    "gruppe" => $gruppe,
    "oppgave" => $oppgave,
    "ressurser" => $ressurser,
    "antall_diskusjoner" => $antall_diskusjoner,
];

include __DIR__ . "/_layout.php";