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
    <div class="oppgave-title-row">
        <div>
            <h1><?php echo htmlspecialchars($state["oppgave"]["tittel"]); ?></h1>
            <p class="oppgave-meta">Opprettet <?php echo htmlspecialchars($state["oppgave"]["opprettet_på"]); ?></p>
        </div>

        <div>
            <a
                class="button button-secondary"
                href="/diskusjon.php?gruppe_id=<?php echo $state["gruppe"]["id"]; ?>&oppgave_id=<?php echo $state["oppgave"]["oppgave_id"]; ?>"
            >
                Se diskusjonstråden
            </a>
        </div>
    </div>

    <p class="oppgave-beskrivelse"><?php echo htmlspecialchars($state["oppgave"]["beskrivelse"]); ?></p>

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
                <?php foreach ($state["ressurser"] as $ressurs): ?>
                    <tr>
                        <th scope="row"><?php echo htmlspecialchars($ressurs["fil_navn"]); ?></th>
                        <td><?php echo htmlspecialchars($ressurs["fil_type"]); ?></td>
                        <td><?php echo bytes_til_menneske($ressurs["fil_størrelse"]); ?></td>
                        <td><?php echo htmlspecialchars($ressurs["siste_versjon"]["versjon_nummer"]); ?></td>
                        <td>
                            <a
                                role="button"
                                class="button button-primary"
                                href="/ressurs.php?ressurs_id=<?php echo $ressurs["fil_id"]; ?>"
                            >
                                Åpne
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
</section>
