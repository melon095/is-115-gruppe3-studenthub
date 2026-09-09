<?php

function send_invitasjon_epost(string $til, string $gruppe_navn, string $lenke): void {
    $emne = "Du er invitert til " . $gruppe_navn . " på Studenthub";
    $melding = <<<EOF
    Hei!
    
    Du har blitt invitert til å bli med i gruppen "$gruppe_navn" på Studenthub.
    
    Trykk på lenken under for å bli med: $lenke.
    
    Hilsen Studenthub
    EOF;

    $headers = "Content-Type: text/plain; charset=UTF-8\r\n";

    // TODO: Skru på når e-postutsending er klar
    // mail($til, $emne, $melding, $headers);
}

function bygg_invitasjonslenke(string $gruppe_id): string {
    $skjema = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $kode = bin2hex(random_bytes(16));
    // TODO: Skriv kode til database.

    return $skjema . '://' . $_SERVER['HTTP_HOST'] . '/inviter-til-gruppe.php?gruppe_id=' . $gruppe_id . '&kode=' . $kode;
}

$feil = [];
$sendt_til = null;
$generert_lenke = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // TODO: Rense bruker input
    if (!csrf_gyldig($_POST['csrf_token'] ?? null)) {
        $feil[] = "Skjemaet er utløpt. Prøv igjen.";
    } else {
        // TODO: Rense bruker input
        $handling = $_POST['handling'] ?? '';

        if ($handling === 'send_epost') {
            // TODO: Resnse bruker input
            $epost = trim($_POST['epost'] ?? '');

            if (!filter_var($epost, FILTER_VALIDATE_EMAIL)) {
                $feil[] = "Skriv inn en gyldig e-postadresse.";
            } else {
                $lenke = bygg_invitasjonslenke($state["gruppe"]["id"]);
                send_invitasjon_epost($epost, $state["gruppe"]["navn"], $lenke);
                $sendt_til = $epost;
                $generert_lenke = $lenke;
            }
        } elseif ($handling === 'generer_lenke') {
            $generert_lenke = bygg_invitasjonslenke($state["gruppe"]["id"]);
        }
    }
}

?>

<section>
    <h1>Inviter til <?php echo htmlspecialchars($state["gruppe"]["navn"]); ?></h1>

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
                Invitasjonen til <strong><?php echo htmlspecialchars($sendt_til); ?></strong> er klargjort.
            </p>
        </div>
    <?php endif; ?>

    <?php if ($generert_lenke !== null): ?>
        <div class="card inviter-resultat">
            <label for="invitasjonslenke">Invitasjonslenke</label>
            <div class="inviter-lenke-rad">
                <input type="text" id="invitasjonslenke" value="<?php echo htmlspecialchars($generert_lenke); ?>" readonly>
                <button type="button" class="button button-secondary" data-kopier-mal="invitasjonslenke">Kopier</button>
            </div>
        </div>
    <?php endif; ?>

    <div class="inviter-metoder">
        <section class="card">
            <h2>Inviter via e-post</h2>
            <p>Vi klargjør en invitasjon til den oppgitte adressen.</p>

            <form method="post" action="" class="form">
                <?php echo csrf_felt(); ?>
                <input type="hidden" name="gruppe_id" value="<?php echo htmlspecialchars($state["gruppe"]["id"]); ?>">
                <input type="hidden" name="handling" value="send_epost">

                <div class="form-felt">
                    <label for="epost">E-postadresse</label>
                    <input type="email" id="epost" name="epost" required>
                </div>

                <button type="submit" class="button button-primary">Send invitasjon</button>
            </form>
        </section>

        <section class="card">
            <h2>Inviter via lenke</h2>
            <p>Generer en lenke du kan sende selv, f.eks. i en chat.</p>

            <form method="post" action="" class="form">
                <?php echo csrf_felt(); ?>
                <input type="hidden" name="gruppe_id" value="<?php echo htmlspecialchars($state["gruppe"]["id"]); ?>">
                <input type="hidden" name="handling" value="generer_lenke">

                <button type="submit" class="button button-secondary">Generer invitasjonslenke</button>
            </form>
        </section>
    </div>
</section>
