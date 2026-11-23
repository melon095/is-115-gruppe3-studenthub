<?php

require_once __DIR__ . '/../public/bootstrap.inc.php';

function lag_nyhetsbrev(string $html_filbane, array $variabler = array()): string
{
    // https://stackoverflow.com/a/17884383
    if (!file_exists($html_filbane)) {
        return false;
    }

    extract($variabler, EXTR_SKIP | EXTR_PREFIX_ALL, "nb");
    ob_start();
    include $html_filbane;

    return ob_get_clean();
}

function hent_studenter_med_gruppemedlemskap(PDO $pdo): array
{
    $stmt = $pdo->prepare("
        SELECT
            s.id AS student_id,
            s.fornavn,
            s.etternavn,
            s.epost,
            g.id AS gruppe_id,
            g.navn,
            g.beskrivelse,
            gm.bruker_id IS NOT NULL AS er_medlem
        FROM studenter s
        CROSS JOIN grupper g
        LEFT JOIN gruppe_medlemmer gm
            ON gm.gruppe_id = g.id
            AND gm.bruker_id = s.id
        ORDER BY s.id, g.navn
    ");

    $stmt->execute();

    $studenter = [];

    foreach ($stmt as $rad) {
        $studenter[$rad["student_id"]] ??= [
            "id" => $rad["student_id"],
            "fornavn" => $rad["fornavn"],
            "etternavn" => $rad["etternavn"],
            "epost" => $rad["epost"],
            "medlem_av" => [],
            "ikke_medlem_av" => [],
        ];

        $nokkel = $rad["er_medlem"] ? "medlem_av" : "ikke_medlem_av";

        $studenter[$rad["student_id"]][$nokkel][] = [
            "gruppe_id" => $rad["gruppe_id"],
            "navn" => $rad["navn"],
            "beskrivelse" => $rad["beskrivelse"],
        ];
    }

    return array_values($studenter);
}

function lag_nyheter(string $nettsted_url, array $student): array
{
    $nettsted_url = rtrim($nettsted_url, "/");
    $forste_gruppe = $student["medlem_av"][0]["gruppe_id"] ?? null;

    $gruppe_side = fn(string $seksjon): string => $forste_gruppe
        ? $nettsted_url . "/gruppe.php?gruppe_id=" . (int) $forste_gruppe . "&section=" . $seksjon
        : $nettsted_url . "/index.php";

    return [
        [
            "tittel" => "Lær mer i PHP-dokumentasjonen",
            "tekst" => "Står du fast i et prosjekt? Den offisielle PHP-dokumentasjonen forklarer funksjoner, språket og eksempler steg for steg.",
            "knapp" => "Åpne dokumentasjonen",
            "lenke" => "https://www.php.net/manual/en/",
        ],
        [
            "tittel" => "Inviter medlemmer via e-post",
            "tekst" => "Nå kan du invitere andre til gruppen din med e-post. Skriv inn e-postadressen, så sender vi invitasjonen for deg.",
            "knapp" => "Inviter til gruppen",
            "lenke" => $gruppe_side("medlemmer"),
        ],
        [
            "tittel" => "Flere versjoner av samme fil",
            "tekst" => "Du kan nå laste opp nye versjoner av en fil og beholde de gamle. Ingenting går tapt når gruppen oppdaterer et dokument.",
            "knapp" => "Se gruppens ressurser",
            "lenke" => $gruppe_side("ressurser"),
        ],
    ];
}

define("TEMPLATE_FIL", __DIR__ . "/../pages/nyhetsbrev.tpl.php");
define("UT_MAPPE", __DIR__ . "/../eksport/nyhetsbrev/");

if (!is_dir(UT_MAPPE)) {
    mkdir(UT_MAPPE, recursive: true);
}

$studenter = hent_studenter_med_gruppemedlemskap($pdo);

foreach ($studenter as $student) {
    $variabler = [
        "student" => $student,
        "nettsted_url" => NETTSTED_URL,
        "nyheter" => lag_nyheter(NETTSTED_URL, $student),
        "kontakt" => [
            "epost" => KONTAKT_EPOST,
            "telefon" => KONTAKT_TELEFON,
        ],
        "grupper" => $student["medlem_av"],
        "grupper_ikke_medlem" => $student["ikke_medlem_av"],
    ];

    $html = lag_nyhetsbrev(TEMPLATE_FIL, $variabler);
    $headers = [
        "From: Studenthub <" . EPOST_AVSENDER . ">",
        "To: " . mb_encode_mimeheader(fullt_navn($student), "UTF-8") . " <$student[epost]>",
        "Subject: Nytt nyhetsbrev fra Studenthub!",
        "Date: " . date('r'),
        "MIME-Version: 1.0",
        "Content-Type: text/html; charset=UTF-8",
        "Content-Transfer-Encoding: 8bit",
    ];

    $eml_innhold = implode("\r\n", $headers) . "\r\n\r\n" . $html;
    $eml_filbane = UT_MAPPE . "$student[id].eml";
    file_put_contents($eml_filbane, $eml_innhold);

    echo "Skrivet til " . realpath($eml_filbane) . PHP_EOL;
}

echo "Ferdig!" . PHP_EOL;
