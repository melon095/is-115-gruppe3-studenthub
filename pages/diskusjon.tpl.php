<?php

?>

<section>
    <div class="diskusjon-title-row">
        <h1>Diskusjon</h1>
        <p class="diskusjon-meta">
            <?php if ($state["oppgave"] !== null): ?>
                Tilhører oppgaven <?php echo htmlspecialchars($state["oppgave"]["tittel"]); ?>
            <?php else: ?>
                Tilhører <?php echo htmlspecialchars($state["gruppe"]["navn"]); ?>
            <?php endif; ?>
        </p>
    </div>

    <ul class="diskusjon-innlegg-liste">
        <?php foreach ($state["innlegg"] as $post): ?>
            <li>
                <article class="diskusjon-innlegg">
                    <img class="diskusjon-innlegg-avatar" src="<?php echo htmlspecialchars($post["forfatter_avatar"]); ?>" alt="Profilbilde" />

                    <div class="diskusjon-innlegg-innhold">
                        <header class="diskusjon-innlegg-header">
                            <span class="diskusjon-innlegg-forfatter"><?php echo htmlspecialchars($post["forfatter_navn"]); ?></span>
                            <time class="diskusjon-innlegg-tid"><?php echo htmlspecialchars($post["opprettet_på"]); ?></time>
                        </header>

                        <p><?php echo htmlspecialchars($post["tekst"]); ?></p>

                        <div class="diskusjon-reaksjoner">
                            <?php foreach ($post["reaksjoner"] as $reaksjon): ?>
                                <button type="button" class="reaksjon-pill" title="<?php echo htmlspecialchars($reaksjon["navn"]); ?>">
                                    <span aria-hidden="true"><?php echo $reaksjon["emoji"]; ?></span>
                                    <span class="reaksjon-antall"><?php echo (int) $reaksjon["antall"]; ?></span>
                                </button>
                            <?php endforeach; ?>

                            <button type="button" class="reaksjon-pill reaksjon-legg-til" aria-label="Legg til reaksjon">+</button>
                        </div>
                    </div>
                </article>
            </li>
        <?php endforeach; ?>
    </ul>

    <form class="diskusjon-nytt-innlegg" method="post">
        <label for="nytt_innlegg">Skriv et innlegg</label>
        <textarea id="nytt_innlegg" name="tekst" rows="3" required></textarea>

        <button type="submit" class="button button-primary button-lg">Send</button>
    </form>
</section>
