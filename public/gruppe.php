<?php

require __DIR__ . "/_bootstrap.php";

/*
|--------------------------------------------------------------------------
| Hent og valider GET-parametere
|--------------------------------------------------------------------------
*/

$get = filter_input_array(INPUT_GET, [
    "gruppe_id" => FILTER_VALIDATE_INT,
    "section" => FILTER_DEFAULT,
    "oppgave_id" => FILTER_VALIDATE_INT,
    "fil_id" => FILTER_VALIDATE_INT,
]);

$gruppe_id = $get["gruppe_id"] ?? null;
$section = $get["section"] ?? null;

if (!$gruppe_id) {
    header("Location: " . url("/index.php"));
    exit();
}

if ($section === null || $section === "") {
    header("Location: " . url("/gruppe.php?gruppe_id=" . $gruppe_id . "&section=oppgaver"));
    exit();
}

/*
|--------------------------------------------------------------------------
| Hent gruppe
|--------------------------------------------------------------------------
*/

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

/*
|--------------------------------------------------------------------------
| Endre gruppenavn
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["gruppe_navn"])) {
    $input = filter_input_array(INPUT_POST, [
        "gruppe_navn" => FILTER_DEFAULT
    ]);

    $nytt_navn = trim($input["gruppe_navn"] ?? "");

    if ($nytt_navn !== "") {
        $stmt = $pdo->prepare("
            UPDATE grupper
            SET navn = :navn
            WHERE id = :gruppe_id
        ");

        $stmt->execute([
            "navn" => $nytt_navn,
            "gruppe_id" => $gruppe_id
        ]);

        $gruppe["navn"] = $nytt_navn;
    }
}

/*
|--------------------------------------------------------------------------
| Opprett oppgave
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["oppgave_tittel"])) {
    $input = filter_input_array(INPUT_POST, [
        "oppgave_tittel" => FILTER_DEFAULT,
        "oppgave_beskrivelse" => FILTER_DEFAULT
    ]);

    $ny_tittel = trim($input["oppgave_tittel"] ?? "");
    $ny_beskrivelse = trim($input["oppgave_beskrivelse"] ?? "");

    if ($ny_tittel !== "") {
        $stmt = $pdo->prepare("
            INSERT INTO oppgaver (gruppe_id, tittel, beskrivelse)
            VALUES (:gruppe_id, :tittel, :beskrivelse)
        ");

        $stmt->execute([
            "gruppe_id" => $gruppe_id,
            "tittel" => $ny_tittel,
            "beskrivelse" => $ny_beskrivelse
        ]);
    }
}

/*
|--------------------------------------------------------------------------
| Hent oppgaver
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT
        id AS oppgave_id,
        gruppe_id,
        tittel,
        beskrivelse,
        opprettet_på
    FROM oppgaver
    WHERE gruppe_id = :gruppe_id
    ORDER BY opprettet_på DESC
");

$stmt->execute([
    "gruppe_id" => $gruppe_id
]);

$oppgaver = $stmt->fetchAll(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| Hent medlemmer
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT
        s.id AS student_id,
        s.fornavn,
        s.etternavn,
        s.avatar_link
    FROM studenter s
    INNER JOIN gruppe_medlemmer gm
        ON gm.bruker_id = s.id
    WHERE gm.gruppe_id = :gruppe_id
    ORDER BY s.fornavn, s.etternavn
");

$stmt->execute([
    "gruppe_id" => $gruppe_id
]);

$medlemmer = $stmt->fetchAll(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| Last opp fil
|--------------------------------------------------------------------------
*/

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
                opprettet_av,
                fil_navn,
                fil_størrelse,
                fil_type,
                fil_lokasjon_hdd
            )
            VALUES (
                :gruppe_id,
                :opprettet_av,
                :fil_navn,
                :fil_storrelse,
                :fil_type,
                :fil_lokasjon
            )
        ");

        $stmt->execute([
            "gruppe_id" => $gruppe_id,
            "opprettet_av" => $_SESSION["student_id"],
            "fil_navn" => $ny_fil_navn,
            "fil_storrelse" => $fil_storrelse,
            "fil_type" => $fil_type,
            "fil_lokasjon" => $fil_lokasjon
        ]);
    }
}

/*
|--------------------------------------------------------------------------
| Hent ressurser/filer
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT
        f.id AS fil_id,
        f.oppgave_id,
        f.opprettet_av,
        CONCAT(s.fornavn, ' ', s.etternavn) AS opprettet_av_navn,
        f.fil_navn,
        f.fil_størrelse,
        f.fil_type,
        f.opprettet_på
    FROM filer f
    INNER JOIN studenter s
        ON s.id = f.opprettet_av
    WHERE f.gruppe_id = :gruppe_id
    ORDER BY f.opprettet_på DESC
");

$stmt->execute([
    "gruppe_id" => $gruppe_id
]);

$ressurser = $stmt->fetchAll(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| Hent filversjoner
|--------------------------------------------------------------------------
*/

foreach ($ressurser as &$ressurs) {
    $stmt = $pdo->prepare("
        SELECT
            id AS versjon_id,
            fil_id,
            opprettet_av,
            versjon_nummer,
            fil_lokasjon_hdd,
            opprettet_på
        FROM filversjoner
        WHERE fil_id = :fil_id
        ORDER BY versjon_nummer ASC
    ");

    $stmt->execute([
        "fil_id" => $ressurs["fil_id"]
    ]);

    $versjoner = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $ressurs["versjoner"] = $versjoner;

    if (!empty($versjoner)) {
        $ressurs["siste_versjon"] = end($versjoner);
    } else {
        $ressurs["siste_versjon"] = [
            "versjon_nummer" => 1
        ];
    }
}

unset($ressurs);

/*
|--------------------------------------------------------------------------
| Opprett diskusjon
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["diskusjon_tittel"])) {
    $input = filter_input_array(INPUT_POST, [
        "diskusjon_tittel" => FILTER_DEFAULT,
        "diskusjon_oppgave_id" => FILTER_VALIDATE_INT,
        "diskusjon_fil_id" => FILTER_VALIDATE_INT
    ]);

    $ny_tittel = trim($input["diskusjon_tittel"] ?? "");
    $merket_oppgave_id = $input["diskusjon_oppgave_id"] ?? null;
    $merket_fil_id = $input["diskusjon_fil_id"] ?? null;

    if ($ny_tittel !== "") {
        $stmt = $pdo->prepare("
            INSERT INTO diskusjoner (
                gruppe_id,
                tittel,
                oppgave_id,
                fil_id,
                opprettet_av
            )
            VALUES (
                :gruppe_id,
                :tittel,
                :oppgave_id,
                :fil_id,
                :opprettet_av
            )
        ");

        $stmt->execute([
            "gruppe_id" => $gruppe_id,
            "tittel" => $ny_tittel,
            "oppgave_id" => $merket_oppgave_id ?: null,
            "fil_id" => $merket_fil_id ?: null,
            "opprettet_av" => $_SESSION["student_id"]
        ]);
    }
}

/*
|--------------------------------------------------------------------------
| Hent diskusjoner
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT
        d.id,
        d.tittel,
        d.oppgave_id,
        d.fil_id,
        d.opprettet_på AS siste_aktivitet,
        COUNT(i.id) AS antall_innlegg
    FROM diskusjoner d
    LEFT JOIN diskusjon_innlegg i
        ON i.diskusjon_id = d.id
    WHERE d.gruppe_id = :gruppe_id
    GROUP BY
        d.id,
        d.tittel,
        d.oppgave_id,
        d.fil_id,
        d.opprettet_på
    ORDER BY d.opprettet_på DESC
");

$stmt->execute([
    "gruppe_id" => $gruppe_id
]);

$diskusjoner = $stmt->fetchAll(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| Filtrering av diskusjoner
|--------------------------------------------------------------------------
*/

$filter_oppgave_id = $get["oppgave_id"] ?? null;
$filter_fil_id = $get["fil_id"] ?? null;

$filter_oppgave = null;

if ($filter_oppgave_id) {
    foreach ($oppgaver as $oppgave) {
        if ((int) $oppgave["oppgave_id"] === (int) $filter_oppgave_id) {
            $filter_oppgave = $oppgave;
            break;
        }
    }
}

$filter_fil = null;

if ($filter_fil_id) {
    foreach ($ressurser as $ressurs) {
        if ((int) $ressurs["fil_id"] === (int) $filter_fil_id) {
            $filter_fil = $ressurs;
            break;
        }
    }
}

$diskusjoner_visning = $diskusjoner;

if ($filter_oppgave_id) {
    $diskusjoner_visning = array_filter(
        $diskusjoner,
        fn($trad) => (int) $trad["oppgave_id"] === (int) $filter_oppgave_id
    );
} elseif ($filter_fil_id) {
    $diskusjoner_visning = array_filter(
        $diskusjoner,
        fn($trad) => (int) $trad["fil_id"] === (int) $filter_fil_id
    );
}

/*
|--------------------------------------------------------------------------
| Sideoppsett
|--------------------------------------------------------------------------
*/

$page_title = "Gruppe";

$page_content = __DIR__ . "/../pages/gruppe.tpl.php";

$page_styles = [
    url("/assets/css/gruppe.css"),
    url("/assets/css/ressurs-tabell.css")
];

$breadcrumbs = [
    [
        "label" => "Grupper",
        "href" => url("/index.php")
    ],
    [
        "label" => $gruppe["navn"]
    ]
];

$state = [
    "gruppe" => $gruppe,
    "oppgaver" => $oppgaver,
    "medlemmer" => $medlemmer,
    "ressurser" => $ressurser,
    "diskusjoner_visning" => $diskusjoner_visning,
    "filter_oppgave" => $filter_oppgave,
    "filter_fil" => $filter_fil,
    "section" => $section
];

include __DIR__ . "/_layout.php";