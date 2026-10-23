<section>
    <div class="oppgave-title-row">
        <div>
            <h1><?php echo htmlspecialchars($tilstand["oppgave"]["tittel"]); ?></h1>
            <p class="oppgave-meta">Opprettet <?php echo htmlspecialchars($tilstand["oppgave"]["opprettet_på"]); ?></p>
        </div>

        <div>
            <?php $diskusjoner_url = url("/gruppe.php?gruppe_id=" . $tilstand["gruppe"]["id"] . "&section=diskusjoner&oppgave_id=" . $tilstand["oppgave"]["oppgave_id"]); ?>
            <a class="button button-secondary" href="<?php echo htmlspecialchars($diskusjoner_url); ?>">
                <?php echo (int) $tilstand["antall_diskusjoner"]; ?> diskusjoner om denne oppgaven
            </a>
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
