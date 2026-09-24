<?php

function section_btn(array $state, string $section, string $str): string
{
    $btnClass = $state["section"] === $section
        ? "primary"
        : "secondary";

    $gruppeId = (int) $state["gruppe"]["id"];

    $href = url(
        "/gruppe.php?gruppe_id={$gruppeId}&section={$section}"
    );

    return '<li>' .
        htmlspecialchars($href) .
        '' .
        htmlspecialchars($str) .
        '</a></li>';
}

function bytes_til_menneske(int $bytes): string
{
    if ($bytes === 0) {
        return "0.00 B";
    }

    $s = ["B", "KB", "MB", "GB", "TB", "PB"];
    $e = floor(log($bytes, 1024));

    return round($bytes / pow(1024, $e), 2) . " " . $s[$e];
}

?>

<section>

    <div class="gruppe-title-row">

        <h1>
            <?php echo htmlspecialchars($state["gruppe"]["navn"]); ?>
        </h1>

        <div class="gruppe-title-knapper">

            <details class="gruppe-endre-navn">

                <summary class="button button-secondary">
                    Endre navn
                </summary>

                <?php
                echo '<form method="post" action="" class="form">';
                ?>

                    <div class="form-felt">

                        <label for="gruppe_navn">
                            Nytt navn
                        </label>

                        <input
                            type="text"
                            id="gruppe_navn"
                            name="gruppe_navn"
                            value="<?php echo htmlspecialchars($state["gruppe"]["navn"]); ?>"
                            required
                        >

                    </div>

                    <button
                        type="submit"
                        class="button button-primary"
                    >
                        Lagre
                    </button>

                </form>

            </details>

            <?php

            $forlat_url = url(
                "/forlat-gruppe.php?gruppe_id=" .
                $state["gruppe"]["id"]
            );

            echo '<a class="button button-warning" href="' .
                htmlspecialchars($forlat_url) .
                '">Forlat gruppe</a>';

            ?>

        </div>

    </div>


    <ul class="gruppe-section-list">

        <?php echo section_btn($state, "oppgaver", "Oppgaver"); ?>

        <?php echo section_btn($state, "medlemmer", "Medlemmer"); ?>

        <?php echo section_btn($state, "ressurser", "Ressurser"); ?>

        <?php echo section_btn($state, "diskusjoner", "Diskusjoner"); ?>

    </ul>


    <div class="gruppe-sections">


        <?php if ($state["section"] === "oppgaver"): ?>

            <section>

                <h2>Oppgaver</h2>

                <?php if (empty($state["oppgaver"])): ?>

                    <p>
                        Gruppen har ingen oppgaver ennå.
                    </p>

                <?php else: ?>

                    <ul>

                        <?php foreach ($state["oppgaver"] as $oppgave): ?>

                            <li>

                                <article class="gruppe-oppgaver-oppgave">

                                    <div>

                                        <h3>
                                            <?php echo htmlspecialchars($oppgave["tittel"]); ?>
                                        </h3>

                                        <p>
                                            <?php echo htmlspecialchars($oppgave["beskrivelse"] ?? ""); ?>
                                        </p>

                                    </div>

                                    <div>

                                        <?php

                                        $oppgave_url = url(
                                            "/oppgave.php?gruppe_id=" .
                                            $state["gruppe"]["id"] .
                                            "&oppgave_id=" .
                                            $oppgave["oppgave_id"]
                                        );

                                        echo '<a class="button button-primary" href="' .
                                            htmlspecialchars($oppgave_url) .
                                            '">Åpne</a>';

                                        ?>

                                    </div>

                                </article>

                            </li>

                        <?php endforeach; ?>

                    </ul>

                <?php endif; ?>


                <?php
                echo '<form method="post" action="" class="form gruppe-ny-oppgave">';
                ?>

                    <div class="form-felt">

                        <label for="oppgave_tittel">
                            Tittel
                        </label>

                        <input
                            type="text"
                            id="oppgave_tittel"
                            name="oppgave_tittel"
                            required
                        >

                    </div>

                    <div class="form-felt">

                        <label for="oppgave_beskrivelse">
                            Beskrivelse (valgfritt)
                        </label>

                        <textarea
                            id="oppgave_beskrivelse"
                            name="oppgave_beskrivelse"
                            rows="3"
                        ></textarea>

                    </div>

                    <button
                        type="submit"
                        class="button button-primary"
                    >
                        Opprett oppgave
                    </button>

                </form>

            </section>


        <?php elseif ($state["section"] === "medlemmer"): ?>

            <section>

                <div class="gruppe-medlemmer-title-row">

                    <h2>Medlemmer</h2>

                    <?php

                    $inviter_url = url(
                        "/inviter.php?gruppe_id=" .
                        $state["gruppe"]["id"]
                    );

                    echo '<a class="button button-primary" href="' .
                        htmlspecialchars($inviter_url) .
                        '">Inviter medlem</a>';

                    ?>

                </div>


                <?php if (empty($state["medlemmer"])): ?>

                    <p>
                        Gruppen har ingen medlemmer.
                    </p>

                <?php else: ?>

                    <ul>

                        <?php foreach ($state["medlemmer"] as $medlem): ?>

                            <li class="gruppe-medlem">

                                <?php if (!empty($medlem["avatar_link"])): ?>

                                    <?php
                                    echo '<img class="gruppe-medlem-img" src="' .
                                        htmlspecialchars($medlem["avatar_link"]) .
                                        '" alt="Brukerprofilbilde">';
                                    ?>

                                <?php endif; ?>

                                <span class="gruppe-medlem-navn">

                                    <?php echo htmlspecialchars($medlem["fornavn"]); ?>

                                    <?php echo htmlspecialchars($medlem["etternavn"]); ?>

                                </span>

                            </li>

                        <?php endforeach; ?>

                    </ul>

                <?php endif; ?>

            </section>


        <?php elseif ($state["section"] === "ressurser"): ?>

            <section>

                <h2>Ressurser</h2>

                <?php if (empty($state["ressurser"])): ?>

                    <p>
                        Gruppen har ingen ressurser ennå.
                    </p>

                <?php else: ?>

                    <div class="ressurs-tabell-wrapper">

                        <table class="ressurs-tabell">

                            <caption>
                                Ressurser i gruppen.
                            </caption>

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

                                        <th scope="row">
                                            <?php echo htmlspecialchars($ressurs["fil_navn"]); ?>
                                        </th>

                                        <td>
                                            <?php echo htmlspecialchars($ressurs["fil_type"] ?? ""); ?>
                                        </td>

                                        <td>
                                            <?php echo bytes_til_menneske((int) $ressurs["fil_størrelse"]); ?>
                                        </td>

                                        <td>
                                            <?php
                                            echo (int) (
                                                $ressurs["siste_versjon"]["versjon_nummer"]
                                                ?? 1
                                            );
                                            ?>
                                        </td>

                                        <td>

                                            <?php

                                            $ressurs_url = url(
                                                "/ressurs.php?ressurs_id=" .
                                                $ressurs["fil_id"]
                                            );

                                            echo '<a class="button button-primary" href="' .
                                                htmlspecialchars($ressurs_url) .
                                                '">Åpne</a>';

                                            ?>

                                        </td>

                                    </tr>

                                <?php endforeach; ?>

                            </tbody>

                        </table>

                    </div>

                <?php endif; ?>


                <?php
                echo '<form method="post" action="" enctype="multipart/form-data" class="gruppe-last-opp">';
                ?>

                    <div class="form-felt">

                        <label for="ny_fil">
                            Last opp fil til gruppen
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


        <?php elseif ($state["section"] === "diskusjoner"): ?>

            <section>

                <h2>Diskusjoner</h2>


                <?php if (
                    $state["filter_oppgave"] !== null ||
                    $state["filter_fil"] !== null
                ): ?>

                    <p class="gruppe-diskusjon-filter">

                        Viser diskusjoner merket med

                        <?php if ($state["filter_oppgave"] !== null): ?>

                            oppgaven
                            "<?php echo htmlspecialchars($state["filter_oppgave"]["tittel"]); ?>"

                        <?php else: ?>

                            filen
                            "<?php echo htmlspecialchars($state["filter_fil"]["fil_navn"]); ?>"

                        <?php endif; ?>

                        <?php

                        $vis_alle_url = url(
                            "/gruppe.php?gruppe_id=" .
                            $state["gruppe"]["id"] .
                            "&section=diskusjoner"
                        );

                        echo '<a href="' .
                            htmlspecialchars($vis_alle_url) .
                            '">Vis alle</a>';

                        ?>

                    </p>

                <?php endif; ?>


                <?php if (empty($state["diskusjoner_visning"])): ?>

                    <p>
                        Gruppen har ingen diskusjoner ennå.
                    </p>

                <?php else: ?>

                    <ul class="gruppe-diskusjon-liste">

                        <?php foreach ($state["diskusjoner_visning"] as $trad): ?>

                            <li>

                                <?php

                                $diskusjon_url = url(
                                    "/diskusjon.php?gruppe_id=" .
                                    $state["gruppe"]["id"] .
                                    "&diskusjon_id=" .
                                    $trad["id"]
                                );

                                echo '<a class="gruppe-diskusjon-kort" href="' .
                                    htmlspecialchars($diskusjon_url) .
                                    '">';

                                ?>

                                    <span class="gruppe-diskusjon-tittel">
                                        <?php echo htmlspecialchars($trad["tittel"]); ?>
                                    </span>

                                    <span class="gruppe-diskusjon-meta">

                                        <?php echo (int) $trad["antall_innlegg"]; ?>

                                        innlegg, sist aktiv

                                        <?php echo htmlspecialchars($trad["siste_aktivitet"]); ?>


                                        <?php if ($trad["oppgave_id"] !== null): ?>

                                            <span class="gruppe-diskusjon-tag">
                                                Oppgave
                                                <?php echo (int) $trad["oppgave_id"]; ?>
                                            </span>

                                        <?php elseif ($trad["fil_id"] !== null): ?>

                                            <span class="gruppe-diskusjon-tag">
                                                Fil
                                                <?php echo (int) $trad["fil_id"]; ?>
                                            </span>

                                        <?php endif; ?>

                                    </span>

                                </a>

                            </li>

                        <?php endforeach; ?>

                    </ul>

                <?php endif; ?>


                <?php
                echo '<form method="post" action="" class="form gruppe-ny-diskusjon">';
                ?>

                    <div class="form-felt">

                        <label for="diskusjon_tittel">
                            Ny diskusjonstråd
                        </label>

                        <input
                            type="text"
                            id="diskusjon_tittel"
                            name="diskusjon_tittel"
                            placeholder="F.eks. Spørsmål om innlevering"
                            required
                        >

                    </div>

                    <div class="form-felt">

                        <label for="diskusjon_oppgave_id">
                            Knytt til oppgave (valgfritt)
                        </label>

                        <select
                            id="diskusjon_oppgave_id"
                            name="diskusjon_oppgave_id"
                        >

                            <option value="">
                                Ingen
                            </option>

                            <?php foreach ($state["oppgaver"] as $oppgave): ?>

                                <option value="<?php echo (int) $oppgave["oppgave_id"]; ?>">
                                    <?php echo htmlspecialchars($oppgave["tittel"]); ?>
                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>

                    <div class="form-felt">

                        <label for="diskusjon_fil_id">
                            Knytt til fil (valgfritt)
                        </label>

                        <select
                            id="diskusjon_fil_id"
                            name="diskusjon_fil_id"
                        >

                            <option value="">
                                Ingen
                            </option>

                            <?php foreach ($state["ressurser"] as $ressurs): ?>

                                <option value="<?php echo (int) $ressurs["fil_id"]; ?>">
                                    <?php echo htmlspecialchars($ressurs["fil_navn"]); ?>
                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>

                    <button
                        type="submit"
                        class="button button-primary"
                    >
                        Opprett diskusjonstråd
                    </button>

                </form>

            </section>


        <?php else: ?>

            <section>

                <h2>Ugyldig seksjon</h2>

                <p>
                    Seksjonen finnes ikke.
                </p>

            </section>

        <?php endif; ?>

    </div>

</section>