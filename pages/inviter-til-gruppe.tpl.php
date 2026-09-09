<?php

$feil = [];
$har_blitt_medlem = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_gyldig($_POST['csrf_token'] ?? null)) {
        $feil[] = "Skjemaet er utløpt. Last siden på nytt og prøv igjen.";
    } else {
        // TODO: Database integrasjon.
        $har_blitt_medlem = true;
    }
}

?>

<div class="inviter-til-gruppe-wrapper">
    <?php if ($har_blitt_medlem): ?>
        <section class="card" aria-labelledby="godkjent-heading">
            <h1 id="godkjent-heading">Du er nå medlem</h1>
            <p>Du er lagt til i <?php echo htmlspecialchars($state["gruppe"]["navn"]); ?>.</p>

            <a class="button button-primary" href="/gruppe.php?gruppe_id=<?php echo $state["gruppe"]["id"]; ?>&section=medlemmer">Gå til gruppen</a>
        </section>
    <?php else: ?>
        <section class="card" aria-labelledby="invitasjon-heading">
            <h1 id="invitasjon-heading">Du er invitert</h1>
            <p>Du er invitert til å bli med i <strong><?php echo htmlspecialchars($state["gruppe"]["navn"]); ?></strong>.</p>

            <?php if (!empty($feil)): ?>
                <ul class="inviter-til-gruppe-feil" role="alert">
                    <?php foreach ($feil as $melding): ?>
                        <li><?php echo htmlspecialchars($melding); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <form method="post" action="">
                <?php echo csrf_felt(); ?>
                <input type="hidden" name="gruppe_id" value="<?php echo htmlspecialchars($state["gruppe"]["id"]); ?>">

                <button type="submit" class="button button-primary button-lg">Godkjenn</button>
            </form>
        </section>
    <?php endif; ?>
</div>
