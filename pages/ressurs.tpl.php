<section>
    <div class="ressurs-title-row">
        <div>
            <h1><?php echo htmlspecialchars($tilstand["ressurs"]["fil_navn"]); ?></h1>
            <p class="ressurs-meta">
                <?php echo htmlspecialchars(mb_strtoupper($tilstand["ressurs"]["fil_type"] ?? "")); ?>
                -
                <?php echo bytes_til_menneske((int) ($tilstand["ressurs"]["siste_versjon"]["fil_størrelse"] ?? $tilstand["ressurs"]["fil_størrelse"] ?? 0)); ?>
                -
                <?php if ($tilstand["oppgave"] !== null): ?>
                    <?php $oppgave_url = url("/oppgave.php?gruppe_id=" . $tilstand["ressurs"]["gruppe_id"] . "&oppgave_id=" . $tilstand["oppgave"]["oppgave_id"]); ?>
                    Tilhører oppgaven
                    <a href="<?php echo htmlspecialchars($oppgave_url); ?>">
                        <strong><?php echo htmlspecialchars($tilstand["oppgave"]["tittel"]); ?></strong>
                    </a>
                <?php else: ?>
                    Tilhører hele gruppen
                <?php endif; ?>
            </p>
        </div>
    </div>

    <div class="ressurs-sections">
        <section>
            <h2>Revisjonslogg</h2>
            <div class="ressurs-tabell-wrapper">
                <table class="ressurs-tabell">
                    <caption>
                        Alle opplastede versjoner av denne ressursen.
                    </caption>
                    <thead>
                        <tr>
                            <th scope="col">Versjon</th>
                            <th scope="col">Opprettet av</th>
                            <th scope="col">Dato</th>
                            <th scope="col">Størrelse</th>
                            <th scope="col">Handling</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_reverse($tilstand["ressurs"]["versjoner"]) as $versjon): ?>
                            <tr>
                                <th scope="row">v<?php echo (int) $versjon["versjon_nummer"]; ?></th>
                                <td><?php echo htmlspecialchars($versjon["opprettet_av_navn"]); ?></td>
                                <td><?php echo htmlspecialchars($versjon["opprettet_på"]); ?></td>
                                <td><?php echo bytes_til_menneske((int) $versjon["fil_størrelse"]); ?></td>
                                <td>
                                    <?php
                                    if ($versjon["er_original"]) {
                                        $last_ned_url = url("/ressurs.php?ressurs_id=" . $tilstand["ressurs"]["fil_id"] . "&last_ned=original");
                                    } else {
                                        $last_ned_url = url("/ressurs.php?ressurs_id=" . $tilstand["ressurs"]["fil_id"] . "&versjon_id=" . $versjon["versjon_id"]);
                                    }
                                    ?>
                                    <a class="button button-secondary" href="<?php echo htmlspecialchars($last_ned_url); ?>">
                                        Last ned
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <section>
            <h2>Last opp ny versjon</h2>
            <form method="post" action="" enctype="multipart/form-data">
                <?php echo csrf_felt(); ?>
                <div class="form-felt">
                    <label for="ny_fil">
                        Velg fil
                    </label>
                    <input type="file" id="ny_fil" name="ny_fil" required>
                </div>

                <button type="submit" class="button button-primary">
                    Last opp
                </button>
            </form>
        </section>
    </div>
</section>
