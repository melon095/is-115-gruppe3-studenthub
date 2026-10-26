<section>
    <div class="oppgave-title-row">
        <div>
            <h1><?php echo htmlspecialchars($tilstand["oppgave"]["tittel"]); ?></h1>
            <p class="oppgave-meta">Opprettet <?php echo htmlspecialchars($tilstand["oppgave"]["opprettet_på"]); ?></p>
        </div>

        <div class="oppgave-handlinger">
            <?php if ($tilstand["er_eier"]): ?>
                <details class="oppgave-rediger">
                    <summary class="button button-secondary">
                        Rediger oppgave
                    </summary>

                    <form method="post" action="" class="form">
                        <?php echo csrf_felt(); ?>

                        <div class="form-felt">
                            <label for="oppgave_tittel">
                                Tittel
                            </label>
                            <input
                                type="text"
                                id="oppgave_tittel"
                                name="oppgave_tittel"
                                value="<?php echo htmlspecialchars($tilstand["oppgave"]["tittel"]); ?>"
                                required>
                        </div>

                        <div class="form-felt">
                            <label for="oppgave_beskrivelse">
                                Beskrivelse (valgfritt)
                            </label>
                            <textarea
                                id="oppgave_beskrivelse"
                                name="oppgave_beskrivelse"
                                rows="3"><?php echo htmlspecialchars($tilstand["oppgave"]["beskrivelse"] ?? ""); ?></textarea>
                        </div>

                        <button type="submit" class="button button-primary">
                            Lagre
                        </button>
                    </form>
                </details>
            <?php endif; ?>
        </div>
    </div>

    <p class="oppgave-beskrivelse"><?php echo htmlspecialchars($tilstand["oppgave"]["beskrivelse"]); ?></p>

    <section class="card">
        <h2>Ressurser</h2>

        <div class="ressurs-tabell-wrapper">
            <table class="ressurs-tabell">
                <caption>Ressurser knyttet til denne oppgaven.</caption>
                <thead>
                    <tr>
                        <th scope="col">Filnavn</th>
                        <th scope="col">Filtype</th>
                        <th scope="col">Filstørrelse</th>
                        <th scope="col">Revisjoner</th>
                        <th scope="col">Handlinger</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($tilstand["ressurser"] as $ressurs): ?>
                    <tr>
                        <th scope="row"><?php echo htmlspecialchars($ressurs["fil_navn"]); ?></th>
                        <td><?php echo htmlspecialchars($ressurs["fil_type"]); ?></td>
                        <td><?php echo bytes_til_menneske($ressurs["fil_størrelse"]); ?></td>
                        <td><?php echo htmlspecialchars($ressurs["siste_versjon"]["versjon_nummer"]); ?></td>
                        <td>
                            <?php $ressurs_url = url("/ressurs.php?ressurs_id=" . $ressurs["fil_id"]); ?>
                            <a role="button" class="button button-primary" href="<?php echo htmlspecialchars($ressurs_url); ?>">
                                Åpne
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <form method="post" action="" enctype="multipart/form-data" class="oppgave-last-opp">
            <?php echo csrf_felt(); ?>
            <div class="form-felt">
                <label for="ny_fil">Last opp fil til oppgaven</label>
                <input type="file" id="ny_fil" name="ny_fil" required>
            </div>

            <button type="submit" class="button button-primary">Last opp</button>
        </form>
    </section>
</section>
