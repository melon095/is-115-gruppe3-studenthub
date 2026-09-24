<?php

function bytes_til_menneske(int $bytes): string
{
    if ($bytes <= 0) {
        return "0.00 B";
    }

    $enheter = [
        "B",
        "KB",
        "MB",
        "GB",
        "TB",
        "PB"
    ];

    $eksponent =
        (int) floor(
            log($bytes, 1024)
        );

    $eksponent =
        min(
            $eksponent,
            count($enheter) - 1
        );

    return round(
        $bytes / pow(1024, $eksponent),
        2
    ) . " " . $enheter[$eksponent];
}

?>

<section>

    <div class="ressurs-title-row">

        <div>

            <h1>
                <?php echo htmlspecialchars(
                    $state["ressurs"]["fil_navn"]
                ); ?>
            </h1>

            <p class="ressurs-meta">

                <?php echo htmlspecialchars(
                    strtoupper(
                        $state["ressurs"]["fil_type"] ?? ""
                    )
                ); ?>

                ·

                <?php echo bytes_til_menneske(
                    (int) (
                        $state["ressurs"]["siste_versjon"]["fil_størrelse"]
                        ?? $state["ressurs"]["fil_størrelse"]
                        ?? 0
                    )
                ); ?>

                ·

                <?php if ($state["oppgave"] !== null): ?>

                    Tilhører oppgaven

                    <strong>
                        <?php echo htmlspecialchars(
                            $state["oppgave"]["tittel"]
                        ); ?>
                    </strong>

                <?php else: ?>

                    Tilhører hele gruppen

                <?php endif; ?>

            </p>

        </div>


        <div>

            <?php

            $diskusjon_url = url(
                "/gruppe.php?gruppe_id=" .
                $state["ressurs"]["gruppe_id"] .
                "&section=diskusjoner" .
                "&fil_id=" .
                $state["ressurs"]["fil_id"]
            );

            echo '<a class="button button-secondary" href="' .
                htmlspecialchars($diskusjon_url) .
                '">Se diskusjoner om denne filen</a>';

            ?>

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

                        <?php foreach (
                            array_reverse(
                                $state["ressurs"]["versjoner"]
                            )
                            as $versjon
                        ): ?>

                            <tr>

                                <th scope="row">

                                    v<?php echo (int) $versjon["versjon_nummer"]; ?>

                                </th>

                                <td>
                                    <?php echo htmlspecialchars(
                                        $versjon["opprettet_av_navn"]
                                    ); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars(
                                        $versjon["opprettet_på"]
                                    ); ?>
                                </td>

                                <td>
                                    <?php echo bytes_til_menneske(
                                        (int) $versjon["fil_størrelse"]
                                    ); ?>
                                </td>

                                <td>

                                    <?php

                                    if ($versjon["er_original"]) {
                                        $last_ned_url = url(
                                            "/ressurs.php?ressurs_id=" .
                                            $state["ressurs"]["fil_id"] .
                                            "&last_ned=original"
                                        );
                                    } else {
                                        $last_ned_url = url(
                                            "/ressurs.php?ressurs_id=" .
                                            $state["ressurs"]["fil_id"] .
                                            "&versjon_id=" .
                                            $versjon["versjon_id"]
                                        );
                                    }

                                    echo '<a class="button button-secondary" href="' .
                                        htmlspecialchars($last_ned_url) .
                                        '">Last ned</a>';

                                    ?>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        </section>


        <section>

            <h2>Last opp ny versjon</h2>

            <form method="post" action="/ressurs.php" enctype="multipart/form-data"></form>

                <div class="form-felt">

                    <label for="ny_fil">
                        Velg fil
                    </label>

                    <input
                        type="file"
                        id="ny_fil"
                        name="ny_fil"
                        required
                    >

                </div>

                <button
                    type="submit"
                    class="button button-primary"
                >
                    Last opp
                </button>

            </form>

        </section>

    </div>

</section>