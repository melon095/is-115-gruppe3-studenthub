<?php

$feil = [];
$opprettet_gruppe = null;
$innsendt_navn = "";
$innsendt_beskrivelse = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = filter_input_array(INPUT_POST, [
        'navn' => FILTER_DEFAULT,
        'beskrivelse' => FILTER_DEFAULT
    ]);

    $innsendt_navn = trim($input['navn'] ?? '');
    $innsendt_beskrivelse = trim($input['beskrivelse'] ?? '');

    if ($innsendt_navn === '') {
        $feil[] = "Gruppen må ha et navn.";
    }

    if (empty($feil)) {
        $stmt = $pdo->prepare("
            INSERT INTO grupper (navn, beskrivelse)
            VALUES (:navn, :beskrivelse)
        ");

        $stmt->execute([
            'navn' => $innsendt_navn,
            'beskrivelse' => $innsendt_beskrivelse
        ]);

        $gruppe_id = $pdo->lastInsertId();

        $opprettet_gruppe = [
            "id" => $gruppe_id,
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
                <a href="<?php echo url('gruppe?id=' . urlencode($opprettet_gruppe['id'])); ?>"
                >
                    Gå til gruppen
                </a>

                <a href="<?php echo url('grupper'); ?>"
                >
                    Til alle grupper
                </a>
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

            
                <div class="form-felt">
                    <label for="navn">Navn på gruppe</label>
                    <input
                        type="text"
                        id="navn"
                        name="navn"
                        value="<?php echo htmlspecialchars($innsendt_navn); ?>"
                        required
                    >
                </div>

                <div class="form-felt">
                    <label for="beskrivelse">Beskrivelse (valgfritt)</label>
                    <textarea
                        id="beskrivelse"
                        name="beskrivelse"
                        rows="4"
                    ><?php echo htmlspecialchars($innsendt_beskrivelse); ?></textarea>
                </div>

                <button type="submit" class="button button-primary button-lg">
                    Opprett gruppe
                </button>
            </form>
        </section>
    <?php endif; ?>
</div>