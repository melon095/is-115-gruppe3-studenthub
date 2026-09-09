<?php

$har_forlatt = $_SERVER['REQUEST_METHOD'] === 'POST';

if ($har_forlatt) {
    // TODO: Database integrasjon.
}

?>

<div class="forlat-gruppe-wrapper">
    <?php if ($har_forlatt): ?>
        <section class="card" aria-labelledby="forlat-gruppe-resultat-heading">
            <h1 id="forlat-gruppe-resultat-heading">Du har forlatt gruppen</h1>
            <p>Du er ikke lenger medlem av <?php echo htmlspecialchars($state["gruppe"]["navn"]); ?>.</p>

            <a class="button button-primary" href="<?php echo url("/index.php"); ?>">Til forsiden</a>
        </section>
    <?php else: ?>
        <section class="card" aria-labelledby="forlat-gruppe-heading">
            <h1 id="forlat-gruppe-heading">Forlat gruppe</h1>
            <p>
                Er du sikker på at du vil forlate <strong><?php echo htmlspecialchars($state["gruppe"]["navn"]); ?></strong>?
                Du mister tilgangen til gruppens oppgaver, diskusjoner og ressurser.
            </p>

            <form method="post" action="" class="forlat-gruppe-knapper">
                <input type="hidden" name="gruppe_id" value="<?php echo htmlspecialchars($state["gruppe"]["id"]); ?>">

                <a
                    class="button button-secondary"
                    href="<?php echo url("/gruppe.php?gruppe_id=" . $state["gruppe"]["id"]); ?>"
                >
                    Avbryt
                </a>
                <button type="submit" class="button button-warning">Ja, forlat gruppen</button>
            </form>
        </section>
    <?php endif; ?>
</div>
