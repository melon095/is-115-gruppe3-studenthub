<?php

function section_btn(array $state, string $section, string $tekst): string
{
    $aktiv = $state["section"] === $section;

    $klasse = $aktiv
        ? "button button-primary"
        : "button button-secondary";

    $gruppe_id = (int) $state["gruppe"]["id"];

    $href = url(
        "/gruppe.php?gruppe_id=" .
        $gruppe_id .
        "&section=" .
        urlencode($section)
    );

    return '<li>' .
        htmlspecialchars($tekst) .
        '</a>' .
        '</li>';
}

function bytes_til_menneske(int $bytes): string
{
    if ($bytes <= 0) {
        return "0.00 B";
    }

    $enheter = ["B", "KB", "MB", "GB", "TB", "PB"];
    $eksponent = (int) floor(log($bytes, 1024));
    $eksponent = min($eksponent, count($enheter) - 1);

    return round(
        $bytes / pow(1024, $eksponent),
        2
    ) . " " . $enheter[$eksponent];
}

?>

<section class="gruppe-side">

    <div class="gruppe-title-row">

        <div>
            <h1>
                <?php echo htmlspecialchars($state["gruppe"]["navn"]); ?>
            </h1>

            <?php if (!empty($state["gruppe"]["beskrivelse"])): ?>
                <p class="gruppe-beskrivelse">
                    <?php echo htmlspecialchars($state["gruppe"]["beskrivelse"]); ?>
                </p>
            <?php endif; ?>
        </div>

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

    <nav class="gruppe-section-nav" aria-label="Gruppemeny">

        <ul class="gruppe-section-list">

            <?php echo section_btn(
                $state,
                "oppgaver",
                "Oppgaver"
            ); ?>

            <?php echo section_btn(
                $state,
                "medlemmer",
                "Medlemmer"
            ); ?>

            <?php echo section_btn(
                $state,
                "ressurser",
                "Ressurser"
            ); ?>

            <?php echo section_btn(
                $state,
                "diskusjoner",
                "Diskusjoner"
            ); ?>

        </ul>

    </nav>

    <div class="gruppe-sections">

        <?php if ($state["section"] === "oppgaver"): ?>

            <section>

                <div class="gruppe-section-heading">
                    <div>
                        <h2>Oppgaver</h2>
                        <p>Oppgaver som tilhører denne gruppen.</p>
                    </div>
                </div>

                <?php if (empty($state["oppgaver"])): ?>

                    <div class="gruppe-empty">
                        <h3>Ingen oppgaver ennå</h3>

                        <p>
                            Opprett den første oppgaven for gruppen nedenfor.
                        </p>
                    </div>

                <?php else: ?>

                    <ul class="gruppe-oppgaver-liste">

                        <?php foreach ($state["oppgaver"] as $oppgave): ?>

                            <li>

                                <article class="gruppe-oppgaver-oppgave">

                                    <div>
                                        <h3>
                                            <?php echo htmlspecialchars($oppgave["tittel"]); ?>
                                        </h3>

                                        <?php if (!empty($oppgave["beskrivelse"])): ?>

                                            <p>
                                                <?php echo htmlspecialchars($oppgave["beskrivelse"]); ?>
                                            </p>

                                        <?php endif; ?>
                                    </div>

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

                                </article>

                            </li>

                        <?php endforeach; ?>

                    </ul>

                <?php endif; ?>

                <div class="gruppe-opprett-omrade">

                    <h3>Opprett ny oppgave</h3>

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

                </div>

            </section>

        <?php elseif ($state["section"] === "medlemmer"): ?>

            <section>

                <div class="gruppe-medlemmer-title-row">

                    <div>
                        <h2>Medlemmer</h2>
                        <p>Studenter som er medlem av gruppen.</p>
                    </div>

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

                    <div class="gruppe-empty">
                        <p>Gruppen har ingen medlemmer.</p>
                    </div>

                <?php else: ?>

                    <ul class="gruppe-medlemmer-liste">

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

                <div class="gruppe-section-heading">
                    <div>
                        <h2>Ressurser</h2>
                        <p>Filer og ressurser som deles med gruppen.</p>
                    </div>
                </div>

                <?php if (empty($state["ressurser"])): ?>

                    <div class="gruppe-empty">
                        <h3>Ingen ressurser ennå</h3>
                        <p>Last opp den første filen nedenfor.</p>
                    </div>

                <?php else: ?>

                    <div class="ressurs-tabell-wrapper">

                        <table class="ressurs-tabell">

                            <caption>Ressurser i gruppen</caption>

                            <thead>
                                <tr>
                                    <th scope="col">Filnavn</th>
                                    <th scope="col">Filtype</th>
                                    <th scope="col">Filstørrelse</th>
                                    <th scope="col">Revisjoner</th>
                                    <th scope="col">Handling</th>
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
                                            <?php echo (int) (
                                                $ressurs["siste_versjon"]["versjon_nummer"]
                                                ?? 1
                                            ); ?>
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

                <div class="gruppe-opprett-omrade">

                    <h3>Last opp ressurs</h3>

                    <?php
                    echo '<form method="post" action="" enctype="multipart/form-data" class="gruppe-last-opp">';
                    ?>

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

                </div>

            </section>

        <?php elseif ($state["section"] === "diskusjoner"): ?>

            <section>

                <div class="gruppe-section-heading">
                    <div>
                        <h2>Diskusjoner</h2>
                        <p>Diskusjonstråder for gruppen.</p>
                    </div>
                </div>

                <?php if (empty($state["diskusjoner_visning"])): ?>

                    <div class="gruppe-empty">

                        <h3>Ingen diskusjoner ennå</h3>

                        <p>
                            Opprett den første diskusjonstråden nedenfor.
                        </p>

                    </div>

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
                                        innlegg

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

                <div class="gruppe-opprett-omrade">

                    <h3>Ny diskusjonstråd</h3>

                    <?php
                    echo '<form method="post" action="" class="form gruppe-ny-diskusjon">';
                    ?>

                        <div class="form-felt">

                            <label for="diskusjon_tittel">
                                Tittel
                            </label>

                            <input
                                type="text"
                                id="diskusjon_tittel"
                                name="diskusjon_tittel"
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

                                <option value="">Ingen</option>

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

                                <option value="">Ingen</option>

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

                </div>

            </section>

        <?php endif; ?>

    </div>

</section>