<?php

$feil = [];
$opprettet_gruppe = null;
$innsendt_navn = "";
$innsendt_beskrivelse = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // TODO: Ordentlig validering med filter_var osv.
    $innsendt_navn = trim($_POST['navn'] ?? '');
    $innsendt_beskrivelse = trim($_POST['beskrivelse'] ?? '');

    if ($innsendt_navn === '') {
        $feil[] = "Gruppen må ha et navn.";
    }

    if (empty($feil)) {
        // TODO: Database integrasjon.
        $opprettet_gruppe = [
            "id" => random_int(100, 999),
            "navn" => $innsendt_navn,
            "beskrivelse" => $innsendt_beskrivelse,
        ];
    }
}

?>

<div class="opprett-gruppe-wrapper">
    <?php if ($opprettet_gruppe !== null): ?>
        <section class="card opprett-gruppe-resultat" aria-labelledby="opprett-gruppe-resultat-heading">
            <h1 id="opprett-gruppe-resultat-heading">Gruppen ble opprettet</h1>

            <dl>
                <dt>Navn</dt>
                <dd><?php echo htmlspecialchars($opprettet_gruppe["navn"]); ?></dd>

                <?php if ($opprettet_gruppe["beskrivelse"] !== ''): ?>
                    <dt>Beskrivelse</dt>
                    <dd><?php echo htmlspecialchars($opprettet_gruppe["beskrivelse"]); ?></dd>
                <?php endif; ?>
            </dl>

            <div class="opprett-gruppe-resultat-knapper">
                <a class="button button-primary" href="/gruppe.php?gruppe_id=<?php echo $opprettet_gruppe["id"]; ?>&section=oppgaver">Gå til gruppen</a>
                <a class="button button-secondary" href="/index.php">Til alle grupper</a>
            </div>
        </section>
    <?php else: ?>
        <section class="card" aria-labelledby="opprett-gruppe-heading">
            <h1 id="opprett-gruppe-heading">Opprett gruppe</h1>

            <?php if (!empty($feil)): ?>
                <ul class="opprett-gruppe-feil" role="alert">
                    <?php foreach ($feil as $melding): ?>
                        <li><?php echo htmlspecialchars($melding); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <form method="post" action="" class="form">
                <div class="form-felt">
                    <label for="navn">Navn på gruppe</label>
                    <input type="text" id="navn" name="navn" value="<?php echo htmlspecialchars($innsendt_navn); ?>" required>
                </div>

                <div class="form-felt">
                    <label for="beskrivelse">Beskrivelse (valgfritt)</label>
                    <textarea id="beskrivelse" name="beskrivelse" rows="4"><?php echo htmlspecialchars($innsendt_beskrivelse); ?></textarea>
                </div>

                <button type="submit" class="button button-primary button-lg">Opprett gruppe</button>
            </form>
        </section>
    <?php endif; ?>
</div>
