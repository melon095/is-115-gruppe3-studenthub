# StudentHub

Webplattform for studentgrupper (IS-115, gruppe 3). Studenter kan opprette grupper, invitere medlemmer, diskutere oppgaver og dele filer.

## Kom i gang

Krever PHP, MySQL/MariaDB og en webserver med `mod_rewrite` (f.eks. Apache med XAMPP).

1. Kopier `config.example.php` til `config.php`.
2. Endre verdiene i `config.php` slik at de passer systemet ditt.
3. Opprett en database med navnet du valgte i `config.php`.
4. Kjør `database.sql` på databasen, for eksempel i phpMyAdmin.
5. Åpne nettsiden i nettleseren, for eksempel `http://localhost/studenthub/`.

## Mapper

- `public/` - sidene brukeren åpner
- `pages/` og `components/` - maler for sider og felles deler
- `lib/` - hjelpekode (database, e-post, osv.)
- `bin/` - skript som kjøres fra kommandolinjen
- `Dokumentasjon/` - ide og datamodell
