<div class="opprett-gruppe-wrapper">

    <?php if ($opprettet_gruppe !== null): ?>

        <section
            class="card opprett-gruppe-resultat"
            aria-labelledby="opprett-gruppe-resultat-heading"
        >

            <h1 id="opprett-gruppe-resultat-heading">
                Gruppen ble opprettet
            </h1>

            <dl>
                <dt>Navn</dt>

                <dd>
                    <?php echo htmlspecialchars($opprettet_gruppe["navn"]); ?>
                </dd>

                <?php if ($opprettet_gruppe["beskrivelse"] !== ""): ?>

                    <dt>Beskrivelse</dt>

                    <dd>
                        <?php echo htmlspecialchars($opprettet_gruppe["beskrivelse"]); ?>
                    </dd>

                <?php endif; ?>
            </dl>

            <div class="opprett-gruppe-resultat-knapper">

                <?php
                $gruppe_url = url(
                    "/gruppe.php?gruppe_id=" .
                    $opprettet_gruppe["id"] .
                    "&section=oppgaver"
                );

                echo '<a class="button button-primary" href="' .
                    htmlspecialchars($gruppe_url) .
                    '">Gå til gruppen</a>';
                ?>

                <?php
                echo '<a class="button button-secondary" href="' .
                    htmlspecialchars(url("/index.php")) .
                    '">Til alle grupper</a>';
                ?>

            </div>

        </section>

    <?php else: ?>

        <section
            class="card"
            aria-labelledby="opprett-gruppe-heading"
        >

            <h1 id="opprett-gruppe-heading">
                Opprett gruppe
            </h1>

            <?php if (!empty($feil)): ?>

                <ul class="opprett-gruppe-feil" role="alert">

                    <?php foreach ($feil as $melding): ?>

                        <li>
                            <?php echo htmlspecialchars($melding); ?>
                        </li>

                    <?php endforeach; ?>

                </ul>

            <?php endif; ?>

            <?php
            echo '<form method="post" action="" class="form">';
            ?>

                <div class="form-felt">
                    <label for="navn">
                        Navn på gruppe
                    </label>

                    <input
                        type="text"
                        id="navn"
                        name="navn"
                        value="<?php echo htmlspecialchars($innsendt_navn); ?>"
                        required
                    >
                </div>

                <div class="form-felt">
                    <label for="beskrivelse">
                        Beskrivelse (valgfritt)
                    </label>

                    <textarea
                        id="beskrivelse"
                        name="beskrivelse"
                        rows="4"
                    ><?php echo htmlspecialchars($innsendt_beskrivelse); ?></textarea>
                </div>

                <?php
                echo '<button type="submit" class="button button-primary button-lg">';
                echo 'Opprett gruppe';
                echo '</button>';

                echo '</form>';
                ?>

        </section>

    <?php endif; ?>

</div>