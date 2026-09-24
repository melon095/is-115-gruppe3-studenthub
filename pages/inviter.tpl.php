<?php

function send_invitasjon_epost(string $til, string $gruppe_navn, string $lenke): bool {
    $emne = "Du er invitert til " . $gruppe_navn . " på Studenthub";

    $melding = <<<EOF
Hei!

Du har blitt invitert til å bli med i gruppen "$gruppe_navn" på Studenthub.

Trykk på lenken under for å bli med:
$lenke

Hilsen Studenthub
EOF;

    $headers = "Content-Type: text/plain; charset=UTF-8\r\n";

    return mail($til, $emne, $melding, $headers);
}


function bygg_invitasjonslenke(PDO $pdo, string $gruppe_id): string {
    $skjema = (!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off")
        ? "https"
        : "http";

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

    return $skjema
        . "://"
        . $_SERVER["HTTP_HOST"]
        . url(
            "/inviter-til-gruppe.php?gruppe_id="
            . urlencode($gruppe_id)
            . "&kode="
            . urlencode($kode)
        );
}


$feil = [];
$sendt_til = null;
$generert_lenke = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $input = filter_input_array(INPUT_POST, [
        "gruppe_id" => FILTER_VALIDATE_INT,
        "handling" => FILTER_DEFAULT,
        "epost" => FILTER_VALIDATE_EMAIL
    ]);

    if (!csrf_gyldig($_POST["csrf_token"] ?? null)) {
        $feil[] = "Skjemaet er utløpt. Prøv igjen.";
    } elseif (
        !$input ||
        !$input["gruppe_id"] ||
        $input["gruppe_id"] !== (int) $state["gruppe"]["id"]
    ) {
        $feil[] = "Ugyldig gruppe.";
    } else {
        $handling = trim($input["handling"] ?? "");

        if ($handling === "send_epost") {
            $epost = $input["epost"] ?? false;

            if (!$epost) {
                $feil[] = "Skriv inn en gyldig e-postadresse.";
            } else {
                $lenke = bygg_invitasjonslenke(
                    $pdo,
                    (string) $state["gruppe"]["id"]
                );

                if (send_invitasjon_epost(
                    $epost,
                    $state["gruppe"]["navn"],
                    $lenke
                )) {
                    $sendt_til = $epost;
                    $generert_lenke = $lenke;
                } else {
                    $feil[] = "Kunne ikke sende invitasjonen på e-post.";
                }
            }

        } elseif ($handling === "generer_lenke") {
            $generert_lenke = bygg_invitasjonslenke(
                $pdo,
                (string) $state["gruppe"]["id"]
            );

        } else {
            $feil[] = "Ugyldig handling.";
        }
    }
}

?>

<section>
    <h1>
        Inviter til <?php echo htmlspecialchars($state["gruppe"]["navn"]); ?>
    </h1>

    <?php if (!empty($feil)): ?>
        <ul class="inviter-feil" role="alert">
            <?php foreach ($feil as $melding): ?>
                <li><?php echo htmlspecialchars($melding); ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <?php if ($sendt_til !== null): ?>
        <div class="card inviter-resultat">
            <p>
                Invitasjonen til
                <strong><?php echo htmlspecialchars($sendt_til); ?></strong>
                er sendt.
            </p>
        </div>
    <?php endif; ?>

    <?php if ($generert_lenke !== null): ?>
        <div class="card inviter-resultat">
            <label for="invitasjonslenke">Invitasjonslenke</label>

            <div class="inviter-lenke-rad">
                <input
                    type="text"
                    id="invitasjonslenke"
                    value="<?php echo htmlspecialchars($generert_lenke); ?>"
                    readonly
                >

                <button
                    type="button"
                    class="button button-secondary"
                    data-kopier-mal="invitasjonslenke"
                >
                    Kopier
                </button>
            </div>
        </div>
    <?php endif; ?>

    <div class="inviter-metoder">

        <section class="card">
            <h2>Inviter via e-post</h2>

            <p>
                Vi sender en invitasjon til den oppgitte adressen.
            </p>

            
                <?php echo csrf_felt(); ?>

                <input
                    type="hidden"
                    name="gruppe_id"
                    value="<?php echo htmlspecialchars($state["gruppe"]["id"]); ?>"
                >

                <input
                    type="hidden"
                    name="handling"
                    value="send_epost"
                >

                <div class="form-felt">
                    <label for="epost">E-postadresse</label>

                    <input
                        type="email"
                        id="epost"
                        name="epost"
                        required
                    >
                </div>

                <button
                    type="submit"
                    class="button button-primary"
                >
                    Send invitasjon
                </button>
            </form>
        </section>


        <section class="card">
            <h2>Inviter via lenke</h2>

            <p>
                Generer en lenke du kan sende selv, f.eks. i en chat.
            </p>

            
                <?php echo csrf_felt(); ?>

                <input
                    type="hidden"
                    name="gruppe_id"
                    value="<?php echo htmlspecialchars($state["gruppe"]["id"]); ?>"
                >

                <input
                    type="hidden"
                    name="handling"
                    value="generer_lenke"
                >

                <button
                    type="submit"
                    class="button button-secondary"
                >
                    Generer invitasjonslenke
                </button>
            </form>
        </section>

    </div>
</section>