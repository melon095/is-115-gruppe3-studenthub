CREATE TABLE studenter (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    fornavn VARCHAR(100) NOT NULL,
    etternavn VARCHAR(100) NOT NULL,
    epost VARCHAR(255) NOT NULL UNIQUE,
    passord VARCHAR(255) NOT NULL,
    avatar_link VARCHAR(500) DEFAULT NULL,
    opprettet_på DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE grupper (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    navn VARCHAR(255) NOT NULL,
    beskrivelse TEXT DEFAULT NULL,
    opprettet_av INT UNSIGNED DEFAULT NULL,
    opprettet_på DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_grupper_opprettet_av
        FOREIGN KEY (opprettet_av)
        REFERENCES studenter(id)
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE gruppe_medlemmer (
    gruppe_id INT UNSIGNED NOT NULL,
    bruker_id INT UNSIGNED NOT NULL,
    opprettet_på DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (gruppe_id, bruker_id),

    CONSTRAINT fk_gruppe_medlemmer_gruppe
        FOREIGN KEY (gruppe_id)
        REFERENCES grupper(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_gruppe_medlemmer_bruker
        FOREIGN KEY (bruker_id)
        REFERENCES studenter(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE gruppe_invitasjoner (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    gruppe_id INT UNSIGNED NOT NULL,
    kode VARCHAR(64) NOT NULL UNIQUE,
    opprettet_av INT UNSIGNED NOT NULL,
    opprettet_på DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_invitasjoner_gruppe
        FOREIGN KEY (gruppe_id)
        REFERENCES grupper(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_invitasjoner_student
        FOREIGN KEY (opprettet_av)
        REFERENCES studenter(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE oppgaver (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    gruppe_id INT UNSIGNED NOT NULL,
    tittel VARCHAR(255) NOT NULL,
    beskrivelse TEXT DEFAULT NULL,
    opprettet_på DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_oppgaver_gruppe
        FOREIGN KEY (gruppe_id)
        REFERENCES grupper(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE filer (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    gruppe_id INT UNSIGNED NOT NULL,
    oppgave_id INT UNSIGNED DEFAULT NULL,
    opprettet_av INT UNSIGNED NOT NULL,
    fil_navn VARCHAR(255) NOT NULL,
    fil_størrelse BIGINT UNSIGNED NOT NULL DEFAULT 0,
    fil_type VARCHAR(50) DEFAULT NULL,
    fil_lokasjon_hdd VARCHAR(1000) DEFAULT NULL,
    opprettet_på DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_filer_gruppe
        FOREIGN KEY (gruppe_id)
        REFERENCES grupper(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_filer_oppgave
        FOREIGN KEY (oppgave_id)
        REFERENCES oppgaver(id)
        ON DELETE SET NULL,

    CONSTRAINT fk_filer_student
        FOREIGN KEY (opprettet_av)
        REFERENCES studenter(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE filversjoner (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    fil_id INT UNSIGNED NOT NULL,
    opprettet_av INT UNSIGNED NOT NULL,
    versjon_nummer INT UNSIGNED NOT NULL,
    fil_lokasjon_hdd VARCHAR(1000) NOT NULL,
    fil_størrelse BIGINT UNSIGNED NOT NULL DEFAULT 0,
    opprettet_på DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    UNIQUE KEY unik_filversjon (fil_id, versjon_nummer),

    CONSTRAINT fk_filversjoner_fil
        FOREIGN KEY (fil_id)
        REFERENCES filer(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_filversjoner_student
        FOREIGN KEY (opprettet_av)
        REFERENCES studenter(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE diskusjoner (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    gruppe_id INT UNSIGNED NOT NULL,
    oppgave_id INT UNSIGNED DEFAULT NULL,
    fil_id INT UNSIGNED DEFAULT NULL,
    opprettet_av INT UNSIGNED NOT NULL,
    tittel VARCHAR(255) NOT NULL,
    opprettet_på DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_diskusjoner_gruppe
        FOREIGN KEY (gruppe_id)
        REFERENCES grupper(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_diskusjoner_oppgave
        FOREIGN KEY (oppgave_id)
        REFERENCES oppgaver(id)
        ON DELETE SET NULL,

    CONSTRAINT fk_diskusjoner_fil
        FOREIGN KEY (fil_id)
        REFERENCES filer(id)
        ON DELETE SET NULL,

    CONSTRAINT fk_diskusjoner_student
        FOREIGN KEY (opprettet_av)
        REFERENCES studenter(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE diskusjon_innlegg (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    diskusjon_id INT UNSIGNED NOT NULL,
    opprettet_av INT UNSIGNED NOT NULL,
    innhold TEXT NOT NULL,
    opprettet_på DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_diskusjon_innlegg_diskusjon
        FOREIGN KEY (diskusjon_id)
        REFERENCES diskusjoner(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_diskusjon_innlegg_student
        FOREIGN KEY (opprettet_av)
        REFERENCES studenter(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
