<section class="diskusjon-side">
    <div class="diskusjon-title-row">
        <div>
            <h1><?php echo htmlspecialchars($tilstand["diskusjon"]["tittel"]); ?></h1>
            <p class="diskusjon-meta">
                Tilhører
                <?php echo htmlspecialchars($tilstand["gruppe"]["navn"]); ?>
                <?php if ($tilstand["oppgave"] !== null): ?>
                    , knyttet til oppgaven
                    <strong><?php echo htmlspecialchars($tilstand["oppgave"]["tittel"]); ?></strong>
                <?php elseif ($tilstand["fil"] !== null): ?>
                    , knyttet til filen
                    <strong><?php echo htmlspecialchars($tilstand["fil"]["fil_navn"]); ?></strong>
                <?php endif; ?>
            </p>
        </div>

        <?php if ($tilstand["er_eier"]): ?>
            <details class="diskusjon-rediger">
                <summary class="button button-secondary">
                    Rediger tittel
                </summary>

                <form method="post" action="" class="form">
                    <?php echo csrf_felt(); ?>

                    <div class="form-felt">
                        <label for="diskusjon_tittel">
                            Tittel
                        </label>
                        <input
                            type="text"
                            id="diskusjon_tittel"
                            name="diskusjon_tittel"
                            value="<?php echo htmlspecialchars($tilstand["diskusjon"]["tittel"]); ?>"
                            required>
                    </div>

                    <button type="submit" class="button button-primary">
                        Lagre
                    </button>
                </form>
            </details>
        <?php endif; ?>
    </div>

    <?php if (!empty($feil)): ?>
        <ul class="form-feil" role="alert">
            <?php foreach ($feil as $melding): ?>
                <li><?php echo htmlspecialchars($melding); ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <?php if (empty($tilstand["innlegg"])): ?>
        <div class="card diskusjon-empty">
            <h2>
                Ingen innlegg ennå
            </h2>
            <p>
                Start diskusjonen ved å skrive det første innlegget.
            </p>
        </div>
    <?php else: ?>
        <ul class="diskusjon-innlegg-liste">
            <?php foreach ($tilstand["innlegg"] as $post): ?>
                <li>
                    <article class="diskusjon-innlegg">
                        <?php echo profilbilde_html($post, "diskusjon-innlegg-avatar", "diskusjon-innlegg-avatar-plassholder"); ?>
                        <div class="diskusjon-innlegg-innhold">
                            <header class="diskusjon-innlegg-header">
                                <span class="diskusjon-innlegg-forfatter">
                                    <?php echo htmlspecialchars(fullt_navn($post)); ?>
                                </span>
                                <time class="diskusjon-innlegg-tid">
                                    <?php echo htmlspecialchars($post["opprettet_på"]); ?>
                                </time>
                            </header>
                            <p>
                                <?php echo nl2br(htmlspecialchars($post["tekst"])); ?>
                            </p>

                            <div class="diskusjon-innlegg-fot">
                                <div class="diskusjon-reaksjoner">
                                    <?php foreach ($tilstand["reaksjonstyper"] as $type): ?>
                                        <?php $type_id = (int) $type["id"]; ?>
                                        <?php $data = $tilstand["reaksjoner"][(int) $post["innlegg_id"]][$type_id] ?? null; ?>
                                        <?php $antall = $data["antall"] ?? 0; ?>
                                        <?php $har_reagert = $data["har_reagert"] ?? false; ?>
                                        <form method="post" action="" class="diskusjon-reaksjon-form">
                                            <?php echo csrf_felt(); ?>
                                            <input type="hidden" name="innlegg_id" value="<?php echo (int) $post["innlegg_id"]; ?>">
                                            <input type="hidden" name="reaksjon_type_id" value="<?php echo $type_id; ?>">
                                            <button
                                                type="submit"
                                                class="diskusjon-reaksjon-knapp<?php echo $har_reagert ? " er-valgt" : ""; ?>"
                                                title="<?php echo htmlspecialchars($type["navn"]); ?>">
                                                <?php echo htmlspecialchars($type["emoji"]); ?>
                                                <?php if ($antall > 0): ?>
                                                    <span class="diskusjon-reaksjon-antall"><?php echo $antall; ?></span>
                                                <?php endif; ?>
                                            </button>
                                        </form>
                                    <?php endforeach; ?>
                                </div>

                                <?php if ($tilstand["er_eier"]): ?>
                                    <form method="post" action="" class="diskusjon-slett-innlegg-form" onsubmit="return confirm('Vil du slette dette innlegget?');">
                                        <?php echo csrf_felt(); ?>
                                        <input type="hidden" name="slett_innlegg" value="1">
                                        <input type="hidden" name="innlegg_id" value="<?php echo (int) $post["innlegg_id"]; ?>">
                                        <button type="submit" class="button button-warning">
                                            Slett innlegg
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </article>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <div class="diskusjon-skriv">
        <h2>
            Skriv et innlegg
        </h2>
        <form method="post" action="" class="diskusjon-nytt-innlegg form">
            <?php echo csrf_felt(); ?>
            <div class="form-felt">
                <label for="nytt_innlegg">
                    Innlegg
                </label>
                <textarea id="nytt_innlegg" name="tekst" rows="5" placeholder="Skriv innlegget ditt her..." required></textarea>
            </div>
            <button type="submit" class="button button-primary button-lg">
                Send
            </button>
        </form>
    </div>
</section>
