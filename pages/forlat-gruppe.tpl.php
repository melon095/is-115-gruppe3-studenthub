<?php

$har_forlatt = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = filter_input_array(INPUT_POST, [
        'gruppe_id' => FILTER_VALIDATE_INT
    ]);

    if (
        $input &&
        $input['gruppe_id'] &&
        $input['gruppe_id'] === (int) $state["gruppe"]["id"]
    ) {
        $stmt = $pdo->prepare("
            DELETE FROM gruppe_medlemmer
            WHERE gruppe_id = :gruppe_id
            AND bruker_id = :bruker_id
        ");

        $stmt->execute([
            'gruppe_id' => $input['gruppe_id'],
            'bruker_id' => $state['bruker']['id']
        ]);

        $har_forlatt = true;
    }
}

?>

<div class="forlat-gruppe-wrapper">
    <?php if ($har_forlatt): ?>
        <section class="card" aria-labelledby="forlat-gruppe-resultat-heading">
            <h1 id="forlat-gruppe-resultat-heading">Du har forlatt gruppen</h1>

            <p>
                Du er ikke lenger medlem av
                <?php echo htmlspecialchars($state["gruppe"]["navn"]); ?>.
            </p>

            <?php echo url('/index.php'); ?>            >
                Til forsiden
            </a>
        </section>

    <?php else: ?>

        <section class="card" aria-labelledby="forlat-gruppe-heading">
            <h1 id="forlat-gruppe-heading">Forlat gruppe</h1>

            <p>
                Er du sikker på at du vil forlate
                <strong><?php echo htmlspecialchars($state["gruppe"]["navn"]); ?></strong>?
                Du mister tilgangen til gruppens oppgaver, diskusjoner og ressurser.
            </p>

            

                <input
                    type="hidden"
                    name="gruppe_id"
                    value="<?php echo htmlspecialchars($state["gruppe"]["id"]); ?>"
                >

                <a href="<?php echo url('/gruppe.php?gruppe_id=' . $state['gruppe']['id']); ?>">
                    Avbryt
                </a>

                <button type="submit" class="button button-warning">
                    Ja, forlat gruppen
                </button>

            </form>
        </section>

    <?php endif; ?>
</div>