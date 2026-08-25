<?php

function bytes_til_menneske(int $bytes): string
{
    if ($bytes == 0)
        return "0.00 B";

    $s = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
    $e = floor(log($bytes, 1024));

    return round($bytes/pow(1024, $e), 2).$s[$e];
}

?>

<section>
    <div class="ressurs-title-row">
        <div>
            <h1><?php echo htmlspecialchars($state["ressurs"]["fil_navn"]); ?></h1>
            <p class="ressurs-meta">
                <?php echo htmlspecialchars(strtoupper($state["ressurs"]["fil_type"])); ?>
                &middot;
                <?php echo bytes_til_menneske($state["ressurs"]["siste_versjon"]["fil_størrelse"]); ?>
                &middot;
                <?php if ($state["oppgave"] !== null): ?>
                    Tilhører oppgaven <?php echo htmlspecialchars($state["oppgave"]["tittel"]); ?>
                <?php else: ?>
                    Tilhører hele gruppen
                <?php endif; ?>
            </p>
        </div>

        <div>
            <a
                class="button button-secondary"
                href="/diskusjon.php?gruppe_id=<?php echo $state["ressurs"]["gruppe_id"]; ?><?php echo $state["oppgave"] !== null ? "&oppgave_id=" . $state["oppgave"]["oppgave_id"] : ""; ?>"
            >
                Se diskusjonstråden
            </a>
        </div>
    </div>

    <div class="ressurs-sections">
        <section>
            <h2>Revisjonslogg</h2>

            <div class="ressurs-tabell-wrapper">
                <table class="ressurs-tabell">
                    <caption>Alle opplastede versjoner av denne ressursen.</caption>
                    <thead>
                        <tr>
                            <th scope="col">Versjon</th>
                            <th scope="col">Opprettet av</th>
                            <th scope="col">Dato</th>
                            <th scope="col">Størrelse</th>
                            <th scope="col">Handlinger</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach (array_reverse($state["ressurs"]["versjoner"]) as $versjon): ?>
                        <tr>
                            <th scope="row">v<?php echo htmlspecialchars($versjon["versjon_nummer"]); ?></th>
                            <td><?php echo htmlspecialchars($versjon["opprettet_av_navn"]); ?></td>
                            <td><?php echo htmlspecialchars($versjon["opprettet_på"]); ?></td>
                            <td><?php echo bytes_til_menneske($versjon["fil_størrelse"]); ?></td>
                            <td><button type="button" class="button button-secondary">Last ned</button></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <section>
            <h2>Last opp ny versjon</h2>

            <form class="ressurs-opplasting" method="post" enctype="multipart/form-data">
                <div>
                    <label for="ny_fil">Velg fil</label>
                    <input type="file" id="ny_fil" name="ny_fil" required>
                </div>

                <button type="submit" class="button button-primary">Last opp</button>
            </form>
        </section>
    </div>
</section>
