<?php

/**
 * Henter en student basert på id.
 *
 * @return array|false Studentraden, eller false hvis studenten ikke finnes.
 */
function database_hent_student(PDO $pdo, int $id)
{
    $stmt = $pdo->prepare("
        SELECT *
        FROM studenter
        WHERE id = :id
    ");

    $stmt->execute([
        "id" => $id
    ]);

    return $stmt->fetch();
}

/**
 * Henter en student basert på e-postadresse.
 *
 * @return array|false Studentraden, eller false hvis ingen student har denne e-posten.
 */
function database_hent_student_ved_epost(PDO $pdo, string $epost)
{
    $stmt = $pdo->prepare("
        SELECT *
        FROM studenter
        WHERE epost = :epost
    ");

    $stmt->execute([
        "epost" => $epost
    ]);

    return $stmt->fetch();
}

/**
 * Oppretter en ny student i databasen.
 *
 * @return string Id-en til den nyopprettede studenten.
 */
function database_opprett_student(PDO $pdo, string $fornavn, string $etternavn, string $epost, string $passord_hash): string
{
    $stmt = $pdo->prepare("
        INSERT INTO studenter (
            fornavn,
            etternavn,
            epost,
            passord
        )
        VALUES (
            :fornavn,
            :etternavn,
            :epost,
            :passord
        )
    ");

    $stmt->execute([
        "fornavn" => $fornavn,
        "etternavn" => $etternavn,
        "epost" => $epost,
        "passord" => $passord_hash
    ]);

    return $pdo->lastInsertId();
}

/**
 * Oppdaterer profilbildet til en student.
 */
function database_oppdater_avatar(PDO $pdo, int $student_id, string $avatar_link)
{
    $stmt = $pdo->prepare("
        UPDATE studenter
        SET avatar_link = :avatar_link
        WHERE id = :student_id
    ");

    $stmt->execute([
        "avatar_link" => $avatar_link,
        "student_id" => $student_id
    ]);
}

/**
 * Henter en gruppe basert på id.
 *
 * @return array|false Gruppen, eller false hvis den ikke finnes.
 */
function database_hent_gruppe(PDO $pdo, int $id)
{
    $stmt = $pdo->prepare("
        SELECT *
        FROM grupper
        WHERE id = :id
    ");

    $stmt->execute([
        "id" => $id,
    ]);

    return $stmt->fetch();
}

/**
 * Oppretter en ny gruppe.
 *
 * @return string Id-en til den nyopprettede gruppen.
 */
function database_opprett_gruppe(PDO $pdo, string $navn, string $beskrivelse, int $opprettet_av): string
{
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
        "navn" => $navn,
        "beskrivelse" => $beskrivelse,
        "opprettet_av" => $opprettet_av
    ]);

    return $pdo->lastInsertId();
}

/**
 * Oppdaterer navn og beskrivelse for en gruppe.
 */
function database_oppdater_gruppe(PDO $pdo, int $gruppe_id, string $navn, string $beskrivelse)
{
    $stmt = $pdo->prepare("
        UPDATE grupper
        SET navn = :navn,
            beskrivelse = :beskrivelse
        WHERE id = :gruppe_id
    ");

    $stmt->execute([
        "navn" => $navn,
        "beskrivelse" => $beskrivelse,
        "gruppe_id" => $gruppe_id
    ]);
}

/**
 * Henter alle grupper på plattformen sammen med medlemmene deres.
 *
 * @return array En rad per medlem i hver gruppe, slått sammen i kallerkoden.
 */
function database_hent_alle_grupper_med_medlemmer(PDO $pdo): array
{
    $stmt = $pdo->prepare("
        SELECT
            g.id AS gruppe_id,
            g.navn,
            g.beskrivelse,
            g.opprettet_av,
            s.id AS student_id,
            s.fornavn,
            s.etternavn,
            s.avatar_link
        FROM grupper g
        LEFT JOIN gruppe_medlemmer gm
            ON gm.gruppe_id = g.id
        LEFT JOIN studenter s
            ON s.id = gm.bruker_id
        ORDER BY g.navn, s.fornavn, s.etternavn
    ");

    $stmt->execute();

    return $stmt->fetchAll();
}

/**
 * Henter alle gruppene en student er medlem av.
 *
 * @return array Liste over grupper.
 */
function database_hent_grupper_for_student(PDO $pdo, int $student_id): array
{
    $stmt = $pdo->prepare("
        SELECT
            g.id AS gruppe_id,
            g.navn
        FROM grupper g
        INNER JOIN gruppe_medlemmer gm
            ON gm.gruppe_id = g.id
        WHERE gm.bruker_id = :student_id
        ORDER BY g.navn
    ");

    $stmt->execute([
        "student_id" => $student_id
    ]);

    return $stmt->fetchAll();
}

/**
 * Sjekker om en bruker er medlem av en gruppe.
 *
 * @return mixed Sann verdi hvis medlem, ellers false.
 */
function database_er_bruker_medlem_i_gruppe(PDO $pdo, int $gruppe_id, int $bruker_id)
{
    $stmt = $pdo->prepare("
        SELECT 1
        FROM gruppe_medlemmer
        WHERE gruppe_id = :gruppe_id
        AND bruker_id = :bruker_id
        LIMIT 1
    ");

    $stmt->execute([
        "gruppe_id" => $gruppe_id,
        "bruker_id" => $bruker_id
    ]);

    return $stmt->fetchColumn();
}

/**
 * Legger en bruker til som medlem av en gruppe.
 */
function database_legg_til_gruppe_medlem(PDO $pdo, int $gruppe_id, int $bruker_id)
{
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
        "bruker_id" => $bruker_id
    ]);
}

/**
 * Fjerner en bruker som medlem av en gruppe.
 */
function database_fjern_gruppe_medlem(PDO $pdo, int $gruppe_id, int $bruker_id)
{
    $stmt = $pdo->prepare("
        DELETE FROM gruppe_medlemmer
        WHERE gruppe_id = :gruppe_id
        AND bruker_id = :bruker_id
    ");

    $stmt->execute([
        "gruppe_id" => $gruppe_id,
        "bruker_id" => $bruker_id
    ]);
}

/**
 * Henter alle medlemmene i en gruppe.
 *
 * @return array Liste over medlemmer.
 */
function database_hent_medlemmer_for_gruppe(PDO $pdo, int $gruppe_id): array
{
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

    return $stmt->fetchAll();
}

/**
 * Oppretter en invitasjon til en gruppe med en unik kode.
 */
function database_opprett_invitasjon(PDO $pdo, int $gruppe_id, string $kode, int $opprettet_av)
{
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
        "opprettet_av" => $opprettet_av
    ]);
}

/**
 * Henter en invitasjon basert på gruppe og kode.
 *
 * @return array|false Invitasjonen, eller false hvis den ikke finnes.
 */
function database_hent_invitasjon(PDO $pdo, int $gruppe_id, string $kode)
{
    $stmt = $pdo->prepare("
        SELECT
            gi.id,
            gi.gruppe_id,
            gi.kode,
            g.navn
        FROM gruppe_invitasjoner gi
        INNER JOIN grupper g
            ON g.id = gi.gruppe_id
        WHERE gi.gruppe_id = :gruppe_id
        AND gi.kode = :kode
    ");

    $stmt->execute([
        "gruppe_id" => $gruppe_id,
        "kode" => $kode
    ]);

    return $stmt->fetch();
}

/**
 * Fjerner en invitasjon, typisk etter at den er tatt i bruk.
 */
function database_fjern_invitasjon(PDO $pdo, int $invitasjon_id)
{
    $stmt = $pdo->prepare("
        DELETE FROM gruppe_invitasjoner
        WHERE id = :id
    ");

    $stmt->execute([
        "id" => $invitasjon_id
    ]);
}

/**
 * Henter en oppgave, forutsatt at den tilhører riktig gruppe.
 *
 * @return array|false Oppgaven, eller false hvis den ikke finnes.
 */
function database_hent_oppgave(PDO $pdo, int $oppgave_id, int $gruppe_id)
{
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

    return $stmt->fetch();
}

/**
 * Henter alle oppgavene i en gruppe.
 *
 * @return array Liste over oppgaver.
 */
function database_hent_oppgaver_for_gruppe(PDO $pdo, int $gruppe_id): array
{
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

    return $stmt->fetchAll();
}

/**
 * Oppretter en ny oppgave i en gruppe.
 *
 * @return string Id-en til den nye oppgaven.
 */
function database_opprett_oppgave(PDO $pdo, int $gruppe_id, string $tittel, string $beskrivelse): string
{
    $stmt = $pdo->prepare("
        INSERT INTO oppgaver (
            gruppe_id,
            tittel,
            beskrivelse
        )
        VALUES (
            :gruppe_id,
            :tittel,
            :beskrivelse
        )
    ");

    $stmt->execute([
        "gruppe_id" => $gruppe_id,
        "tittel" => $tittel,
        "beskrivelse" => $beskrivelse
    ]);

    return $pdo->lastInsertId();
}

/**
 * Oppdaterer tittel og beskrivelse for en oppgave.
 */
function database_oppdater_oppgave(PDO $pdo, int $oppgave_id, int $gruppe_id, string $tittel, string $beskrivelse)
{
    $stmt = $pdo->prepare("
        UPDATE oppgaver
        SET tittel = :tittel,
            beskrivelse = :beskrivelse
        WHERE id = :oppgave_id
        AND gruppe_id = :gruppe_id
    ");

    $stmt->execute([
        "tittel" => $tittel,
        "beskrivelse" => $beskrivelse,
        "oppgave_id" => $oppgave_id,
        "gruppe_id" => $gruppe_id
    ]);
}

/**
 * Fjerner en oppgave fra en gruppe.
 */
function database_fjern_oppgave(PDO $pdo, int $oppgave_id, int $gruppe_id)
{
    $stmt = $pdo->prepare("
        DELETE FROM oppgaver
        WHERE id = :oppgave_id
        AND gruppe_id = :gruppe_id
    ");

    $stmt->execute([
        "oppgave_id" => $oppgave_id,
        "gruppe_id" => $gruppe_id
    ]);
}

/**
 * Henter grunnleggende informasjon om en fil, forutsatt at den tilhører riktig gruppe.
 *
 * @return array|false Filen, eller false hvis den ikke finnes.
 */
function database_hent_fil(PDO $pdo, int $fil_id, int $gruppe_id)
{
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
        "fil_id" => $fil_id,
        "gruppe_id" => $gruppe_id
    ]);

    return $stmt->fetch();
}

/**
 * Henter full informasjon om en ressurs, inkludert navnet til personen som lastet den opp.
 *
 * @return array|false Ressursen, eller false hvis den ikke finnes.
 */
function database_hent_ressurs(PDO $pdo, int $fil_id)
{
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
        WHERE f.id = :fil_id
    ");

    $stmt->execute([
        "fil_id" => $fil_id
    ]);

    return $stmt->fetch();
}

/**
 * Henter alle ressursene som tilhører en gruppe.
 *
 * @return array Liste over ressurser.
 */
function database_hent_ressurser_for_gruppe(PDO $pdo, int $gruppe_id): array
{
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

    return $stmt->fetchAll();
}

/**
 * Henter ressursene som er knyttet til en spesifikk oppgave.
 *
 * @return array Liste over ressurser.
 */
function database_hent_ressurser_for_oppgave(PDO $pdo, int $gruppe_id, int $oppgave_id): array
{
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

    return $stmt->fetchAll();
}

/**
 * Registrerer en nylig opplastet fil i databasen.
 *
 * @return string Id-en til den nye filen.
 */
function database_opprett_fil(PDO $pdo, int $gruppe_id, ?int $oppgave_id, int $opprettet_av, string $fil_navn, int $fil_størrelse, string $fil_type, string $fil_lokasjon): string
{
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
        "opprettet_av" => $opprettet_av,
        "fil_navn" => $fil_navn,
        "fil_storrelse" => $fil_størrelse,
        "fil_type" => $fil_type,
        "fil_lokasjon" => $fil_lokasjon
    ]);

    return $pdo->lastInsertId();
}

/**
 * Fjerner en fil fra en gruppe.
 */
function database_fjern_fil(PDO $pdo, int $fil_id, int $gruppe_id)
{
    $stmt = $pdo->prepare("
        DELETE FROM filer
        WHERE id = :fil_id
        AND gruppe_id = :gruppe_id
    ");

    $stmt->execute([
        "fil_id" => $fil_id,
        "gruppe_id" => $gruppe_id
    ]);
}

/**
 * Henter den nyeste versjonen av en fil.
 *
 * @return array|false Den nyeste versjonen, eller false hvis filen ikke har noen versjoner.
 */
function database_hent_siste_filversjon(PDO $pdo, int $fil_id)
{
    $stmt = $pdo->prepare("
        SELECT
            id AS versjon_id,
            versjon_nummer
        FROM filversjoner
        WHERE fil_id = :fil_id
        ORDER BY versjon_nummer DESC
        LIMIT 1
    ");

    $stmt->execute([
        "fil_id" => $fil_id
    ]);

    return $stmt->fetch();
}

/**
 * Henter alle versjonene som finnes av en fil.
 *
 * @return array Liste over filversjoner.
 */
function database_hent_filversjoner_for_fil(PDO $pdo, int $fil_id): array
{
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
        "fil_id" => $fil_id
    ]);

    return $stmt->fetchAll();
}

/**
 * Henter en spesifikk filversjon, forutsatt at den tilhører riktig fil.
 *
 * @return array|false Filversjonen, eller false hvis den ikke finnes.
 */
function database_hent_filversjon(PDO $pdo, int $versjon_id, int $fil_id)
{
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
        "fil_id" => $fil_id
    ]);

    return $stmt->fetch();
}

/**
 * Finner neste ledige versjonsnummer for en fil.
 *
 * @return int Neste versjonsnummer.
 */
function database_hent_neste_versjonsnummer(PDO $pdo, int $fil_id): int
{
    $stmt = $pdo->prepare("
        SELECT COALESCE(MAX(versjon_nummer), 1) + 1
        FROM filversjoner
        WHERE fil_id = :fil_id
    ");

    $stmt->execute([
        "fil_id" => $fil_id
    ]);

    return (int) $stmt->fetchColumn();
}

/**
 * Registrerer en ny versjon av en fil.
 *
 * @return string Id-en til den nye filversjonen.
 */
function database_opprett_filversjon(PDO $pdo, int $fil_id, int $opprettet_av, int $versjon_nummer, string $fil_lokasjon, int $fil_størrelse): string
{
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
        "fil_id" => $fil_id,
        "opprettet_av" => $opprettet_av,
        "versjon_nummer" => $versjon_nummer,
        "fil_lokasjon" => $fil_lokasjon,
        "fil_storrelse" => $fil_størrelse
    ]);

    return $pdo->lastInsertId();
}

/**
 * Henter en diskusjon, forutsatt at den tilhører riktig gruppe.
 *
 * @return array|false Diskusjonen, eller false hvis den ikke finnes.
 */
function database_hent_diskusjon(PDO $pdo, int $gruppe_id, int $diskusjon_id)
{
    $stmt = $pdo->prepare("
        SELECT *
        FROM diskusjoner
        WHERE id = :diskusjon_id
        AND gruppe_id = :gruppe_id
    ");

    $stmt->execute([
        "diskusjon_id" => $diskusjon_id,
        "gruppe_id" => $gruppe_id
    ]);

    return $stmt->fetch();
}

/**
 * Henter alle diskusjonene i en gruppe, sammen med antall innlegg i hver.
 *
 * @return array Liste over diskusjoner.
 */
function database_hent_diskusjoner_for_gruppe(PDO $pdo, int $gruppe_id): array
{
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

    return $stmt->fetchAll();
}

/**
 * Oppretter en ny diskusjon i en gruppe.
 *
 * @return string Id-en til den nye diskusjonen.
 */
function database_opprett_diskusjon(PDO $pdo, int $gruppe_id, string $tittel, ?int $oppgave_id, ?int $fil_id, int $opprettet_av): string
{
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
        "tittel" => $tittel,
        "oppgave_id" => $oppgave_id,
        "fil_id" => $fil_id,
        "opprettet_av" => $opprettet_av
    ]);

    return $pdo->lastInsertId();
}

/**
 * Oppdaterer tittelen på en diskusjon.
 */
function database_oppdater_diskusjon(PDO $pdo, int $diskusjon_id, int $gruppe_id, string $tittel)
{
    $stmt = $pdo->prepare("
        UPDATE diskusjoner
        SET tittel = :tittel
        WHERE id = :diskusjon_id
        AND gruppe_id = :gruppe_id
    ");

    $stmt->execute([
        "tittel" => $tittel,
        "diskusjon_id" => $diskusjon_id,
        "gruppe_id" => $gruppe_id
    ]);
}

/**
 * Fjerner en diskusjon fra en gruppe.
 */
function database_fjern_diskusjon(PDO $pdo, int $diskusjon_id, int $gruppe_id)
{
    $stmt = $pdo->prepare("
        DELETE FROM diskusjoner
        WHERE id = :diskusjon_id
        AND gruppe_id = :gruppe_id
    ");

    $stmt->execute([
        "diskusjon_id" => $diskusjon_id,
        "gruppe_id" => $gruppe_id
    ]);
}

/**
 * Henter alle innleggene i en diskusjon, sortert etter når de ble opprettet.
 *
 * @return array Liste over innlegg.
 */
function database_hent_innlegger_i_diskusjon(PDO $pdo, int $diskusjon_id): array
{
    $stmt = $pdo->prepare("
        SELECT
            i.id AS innlegg_id,
            i.opprettet_av,
            i.innhold AS tekst,
            i.opprettet_på,
            s.fornavn,
            s.etternavn,
            s.avatar_link
        FROM diskusjon_innlegg i
        INNER JOIN studenter s
            ON s.id = i.opprettet_av
        WHERE i.diskusjon_id = :diskusjon_id
        ORDER BY i.opprettet_på ASC
    ");

    $stmt->execute([
        "diskusjon_id" => $diskusjon_id
    ]);

    return $stmt->fetchAll();
}

/**
 * Oppretter et nytt innlegg i en diskusjon.
 *
 * @return string Id-en til det nye innlegget.
 */
function database_opprett_innlegg(PDO $pdo, int $diskusjon_id, int $bruker_id, string $innhold): string
{
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
        "opprettet_av" => $bruker_id,
        "innhold" => $innhold
    ]);

    return $pdo->lastInsertId();
}

/**
 * Fjerner et innlegg, forutsatt at det tilhører riktig diskusjon og gruppe.
 */
function database_fjern_diskusjon_innlegg(PDO $pdo, int $diskusjon_id, int $gruppe_id, int $innlegg_id)
{
    $stmt = $pdo->prepare("
        DELETE i
        FROM diskusjon_innlegg i
        INNER JOIN diskusjoner d
            ON d.id = i.diskusjon_id
        WHERE i.id = :innlegg_id
        AND d.id = :diskusjon_id
        AND d.gruppe_id = :gruppe_id
    ");

    $stmt->execute([
        "innlegg_id" => $innlegg_id,
        "diskusjon_id" => $diskusjon_id,
        "gruppe_id" => $gruppe_id
    ]);
}

/**
 * Sjekker at et innlegg finnes og tilhører riktig diskusjon.
 *
 * @return array|false Innlegget, eller false hvis det ikke finnes.
 */
function database_hent_innlegg(PDO $pdo, int $innlegg_id, int $diskusjon_id)
{
    $stmt = $pdo->prepare("
        SELECT id
        FROM diskusjon_innlegg
        WHERE id = :innlegg_id
        AND diskusjon_id = :diskusjon_id
    ");

    $stmt->execute([
        "innlegg_id" => $innlegg_id,
        "diskusjon_id" => $diskusjon_id
    ]);

    return $stmt->fetch();
}

/**
 * Henter alle tilgjengelige reaksjonstyper.
 *
 * @return array Liste over reaksjonstyper.
 */
function database_hent_reaksjonstyper(PDO $pdo): array
{
    $stmt = $pdo->prepare("
        SELECT id, emoji, navn
        FROM reaksjon_typer
        ORDER BY id
    ");

    $stmt->execute();

    return $stmt->fetchAll();
}

/**
 * Sjekker om en reaksjonstype faktisk finnes.
 *
 * @return mixed Sann verdi hvis gyldig, ellers false.
 */
function database_er_gyldig_reaksjonstype(PDO $pdo, int $reaksjon_type_id)
{
    $stmt = $pdo->prepare("
        SELECT 1
        FROM reaksjon_typer
        WHERE id = :reaksjon_type_id
        LIMIT 1
    ");

    $stmt->execute([
        "reaksjon_type_id" => $reaksjon_type_id
    ]);

    return $stmt->fetchColumn();
}

/**
 * Henter alle reaksjoner på innleggene i en diskusjon.
 *
 * @return array Liste over reaksjoner.
 */
function database_hent_reaksjoner_for_diskusjon(PDO $pdo, int $diskusjon_id): array
{
    $stmt = $pdo->prepare("
        SELECT
            r.innlegg_id,
            r.reaksjon_type_id,
            r.bruker_id
        FROM reaksjoner r
        INNER JOIN diskusjon_innlegg i
            ON i.id = r.innlegg_id
        WHERE i.diskusjon_id = :diskusjon_id
    ");

    $stmt->execute([
        "diskusjon_id" => $diskusjon_id
    ]);

    return $stmt->fetchAll();
}

/**
 * Sjekker om en bruker allerede har reagert med en gitt reaksjonstype på et innlegg.
 *
 * @return mixed Sann verdi hvis brukeren har reagert, ellers false.
 */
function database_har_reagert(PDO $pdo, int $innlegg_id, int $reaksjon_type_id, int $bruker_id)
{
    $stmt = $pdo->prepare("
        SELECT 1
        FROM reaksjoner
        WHERE innlegg_id = :innlegg_id
        AND reaksjon_type_id = :reaksjon_type_id
        AND bruker_id = :bruker_id
    ");

    $stmt->execute([
        "innlegg_id" => $innlegg_id,
        "reaksjon_type_id" => $reaksjon_type_id,
        "bruker_id" => $bruker_id
    ]);

    return $stmt->fetchColumn();
}

/**
 * Legger til en reaksjon fra en bruker på et innlegg.
 */
function database_legg_til_reaksjon(PDO $pdo, int $innlegg_id, int $reaksjon_type_id, int $bruker_id)
{
    $stmt = $pdo->prepare("
        INSERT INTO reaksjoner (
            innlegg_id,
            reaksjon_type_id,
            bruker_id
        )
        VALUES (
            :innlegg_id,
            :reaksjon_type_id,
            :bruker_id
        )
    ");

    $stmt->execute([
        "innlegg_id" => $innlegg_id,
        "reaksjon_type_id" => $reaksjon_type_id,
        "bruker_id" => $bruker_id
    ]);
}

/**
 * Fjerner en brukers reaksjon på et innlegg.
 */
function database_fjern_reaksjon(PDO $pdo, int $innlegg_id, int $reaksjon_type_id, int $bruker_id)
{
    $stmt = $pdo->prepare("
        DELETE FROM reaksjoner
        WHERE innlegg_id = :innlegg_id
        AND reaksjon_type_id = :reaksjon_type_id
        AND bruker_id = :bruker_id
    ");

    $stmt->execute([
        "innlegg_id" => $innlegg_id,
        "reaksjon_type_id" => $reaksjon_type_id,
        "bruker_id" => $bruker_id
    ]);
}
