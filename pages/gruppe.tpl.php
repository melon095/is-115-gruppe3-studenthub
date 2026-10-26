<?php

function section_btn(array $tilstand, string $section, string $tekst): string
{
    $aktiv = $tilstand["section"] === $section;
    $klasse = $aktiv ? "button button-primary" : "button button-secondary";
    $href = url("/gruppe.php?gruppe_id=" . (int) $tilstand["gruppe"]["id"] . "&section=" . urlencode($section));
    $tekst = htmlspecialchars($tekst);

    return <<<EOF
<li>
    <a class="{$klasse}" href="{$href}">
        {$tekst}
    </a>
</li>
EOF;
}

?>

<section class="gruppe-side">

    <div class="gruppe-title-row">
        <div>
            <h1><?php echo htmlspecialchars($tilstand["gruppe"]["navn"]); ?></h1>

            <?php if (!empty($tilstand["gruppe"]["beskrivelse"])): ?>
                <p class="gruppe-beskrivelse">
                    <?php echo htmlspecialchars($tilstand["gruppe"]["beskrivelse"]); ?>
                </p>
            <?php endif; ?>
        </div>

        <div class="gruppe-title-knapper">
            <?php if ($tilstand["er_eier"]): ?>
                <details class="gruppe-endre-navn">
                    <summary class="button button-secondary">
                        Rediger gruppe
                    </summary>

                    <form method="post" action="" class="form">
                        <?php echo csrf_felt(); ?>

                        <div class="form-felt">
                            <label for="gruppe_navn">
                                Navn
                            </label>

                            <input
                                type="text"
                                id="gruppe_navn"
                                name="gruppe_navn"
                                value="<?php echo htmlspecialchars($tilstand["gruppe"]["navn"]); ?>"
                                required>
                        </div>

                        <div class="form-felt">
                            <label for="gruppe_beskrivelse">
                                Beskrivelse (valgfritt)
                            </label>

                            <textarea
                                id="gruppe_beskrivelse"
                                name="gruppe_beskrivelse"
                                rows="3"><?php echo htmlspecialchars($tilstand["gruppe"]["beskrivelse"] ?? ""); ?></textarea>
                        </div>

                        <button type="submit" class="button button-primary">
                            Lagre
                        </button>
                    </form>
                </details>
            <?php else: ?>
                <?php $forlat_url = url("/forlat-gruppe.php?gruppe_id=" . $tilstand["gruppe"]["id"]); ?>
                <a class="button button-warning" href="<?php echo htmlspecialchars($forlat_url); ?>">
                    Forlat gruppe
                </a>
            <?php endif; ?>
        </div>
    </div>

    <nav class="gruppe-section-nav" aria-label="Gruppemeny">
        <ul class="gruppe-section-list">
            <?php echo section_btn($tilstand, "oppgaver", "Oppgaver"); ?>
            <?php echo section_btn($tilstand, "medlemmer", "Medlemmer"); ?>
            <?php echo section_btn($tilstand, "ressurser", "Ressurser"); ?>
            <?php echo section_btn($tilstand, "diskusjoner", "Diskusjoner"); ?>
        </ul>
    </nav>

    <div class="gruppe-sections">

        <?php if ($tilstand["section"] === "oppgaver"): ?>
            <section>
                <div class="gruppe-section-heading">
                    <div>
                        <h2>Oppgaver</h2>
                        <p>
                            Oppgaver som tilhører denne gruppen.
                        </p>
                    </div>
                </div>

                <?php if (empty($tilstand["oppgaver"])): ?>
                    <div class="gruppe-empty">
                        <h3>
                            Ingen oppgaver ennå
                        </h3>
                        <p>
                            Opprett den første oppgaven nedenfor.
                        </p>
                    </div>
                <?php else: ?>
                    <ul class="gruppe-oppgaver-liste">
                        <?php foreach ($tilstand["oppgaver"] as $oppgave): ?>
                            <li>
                                <article class="gruppe-oppgaver-oppgave">
                                    <div>
                                        <h3><?php echo htmlspecialchars($oppgave["tittel"]); ?></h3>

                                        <?php if (!empty($oppgave["beskrivelse"])): ?>
                                            <p>
                                                <?php echo htmlspecialchars($oppgave["beskrivelse"]); ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>

                                    <div class="gruppe-handlinger">
                                        <?php $oppgave_url = url("/oppgave.php?gruppe_id=" . $tilstand["gruppe"]["id"] . "&oppgave_id=" . $oppgave["oppgave_id"]); ?>
                                        <a class="button button-primary" href="<?php echo htmlspecialchars($oppgave_url); ?>">
                                            Åpne
                                        </a>

                                        <?php if ($tilstand["er_eier"]): ?>
                                            <form method="post" action="" onsubmit="return confirm('Vil du slette denne oppgaven?');">
                                                <?php echo csrf_felt(); ?>
                                                <input type="hidden" name="admin_handling" value="slett_oppgave">
                                                <input type="hidden" name="oppgave_id" value="<?php echo (int) $oppgave["oppgave_id"]; ?>">
                                                <button type="submit" class="button button-warning">
                                                    Slett
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </article>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

                <div class="gruppe-opprett-omrade">
                    <h3>Opprett ny oppgave</h3>

                    <form method="post" action="" class="form gruppe-ny-oppgave">
                        <?php echo csrf_felt(); ?>

                        <div class="form-felt">
                            <label for="oppgave_tittel">
                                Tittel
                            </label>
                            <input type="text" id="oppgave_tittel" name="oppgave_tittel" required>
                        </div>

                        <div class="form-felt">
                            <label for="oppgave_beskrivelse">
                                Beskrivelse (valgfritt)
                            </label>
                            <textarea id="oppgave_beskrivelse" name="oppgave_beskrivelse" rows="3"></textarea>
                        </div>

                        <button type="submit" class="button button-primary">
                            Opprett oppgave
                        </button>
                    </form>
                </div>
            </section>

        <?php elseif ($tilstand["section"] === "medlemmer"): ?>
            <section>
                <div class="gruppe-medlemmer-title-row">
                    <div>
                        <h2>Medlemmer</h2>
                        <p>
                            Studenter som er medlem av gruppen.
                        </p>
                    </div>

                    <?php if ($tilstand["er_eier"]): ?>
                        <?php $inviter_url = url("/inviter.php?gruppe_id=" . $tilstand["gruppe"]["id"]); ?>
                        <a class="button button-primary" href="<?php echo htmlspecialchars($inviter_url); ?>">
                            Inviter medlem
                        </a>
                    <?php endif; ?>
                </div>

                <ul class="gruppe-medlemmer-liste">
                    <?php foreach ($tilstand["medlemmer"] as $medlem): ?>
                        <?php $medlem_er_eier = (int) $medlem["student_id"] === (int) $tilstand["gruppe"]["opprettet_av"]; ?>
                        <?php $medlem_navn = fullt_navn($medlem); ?>

                        <li class="gruppe-medlem">
                            <?php if (!empty($medlem["avatar_link"])): ?>
                                <img class="gruppe-medlem-img" src="<?php echo htmlspecialchars($medlem["avatar_link"]); ?>" alt="<?php echo htmlspecialchars($medlem_navn); ?>">
                            <?php else: ?>
                                <div class="gruppe-medlem-img gruppe-medlem-placeholder" aria-hidden="true">
                                    <?php echo htmlspecialchars(forbokstav($medlem["fornavn"])); ?>
                                </div>
                            <?php endif; ?>

                            <span class="gruppe-medlem-navn">
                                <?php echo htmlspecialchars($medlem["fornavn"]); ?>
                                <?php echo htmlspecialchars($medlem["etternavn"]); ?>

                                <?php if ($medlem_er_eier): ?>
                                    <strong>(Eier)</strong>
                                <?php endif; ?>
                            </span>

                            <?php if ($tilstand["er_eier"] && !$medlem_er_eier): ?>
                                <form method="post" action="" onsubmit="return confirm('Vil du fjerne dette medlemmet fra gruppen?');">
                                    <?php echo csrf_felt(); ?>
                                    <input type="hidden" name="admin_handling" value="fjern_medlem">
                                    <input type="hidden" name="student_id" value="<?php echo (int) $medlem["student_id"]; ?>">
                                    <button type="submit" class="button button-warning">
                                        Fjern medlem
                                    </button>
                                </form>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </section>

        <?php elseif ($tilstand["section"] === "ressurser"): ?>
            <section>
                <div class="gruppe-section-heading">
                    <div>
                        <h2>Ressurser</h2>
                        <p>
                            Filer og ressurser som deles med gruppen.
                        </p>
                    </div>
                </div>

                <?php if (empty($tilstand["ressurser"])): ?>
                    <div class="gruppe-empty">
                        <h3>
                            Ingen ressurser ennå
                        </h3>
                        <p>
                            Last opp den første filen nedenfor.
                        </p>
                    </div>
                <?php else: ?>
                    <div class="ressurs-tabell-wrapper">
                        <table class="ressurs-tabell">
                            <thead>
                                <tr>
                                    <th>Filnavn</th>
                                    <th>Filtype</th>
                                    <th>Filstørrelse</th>
                                    <th>Tilhørighet</th>
                                    <th>Handling</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($tilstand["ressurser"] as $ressurs): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($ressurs["fil_navn"]); ?></td>
                                        <td><?php echo htmlspecialchars($ressurs["fil_type"] ?? ""); ?></td>
                                        <td><?php echo bytes_til_menneske((int) $ressurs["fil_størrelse"]); ?></td>
                                        <td>
                                            <?php $tilhorende_oppgave = $tilstand["oppgave_etter_id"][(int) $ressurs["oppgave_id"]] ?? null; ?>
                                            <?php if ($tilhorende_oppgave !== null): ?>
                                                <?php $tilhorighet_url = url("/oppgave.php?gruppe_id=" . $tilstand["gruppe"]["id"] . "&oppgave_id=" . $tilhorende_oppgave["oppgave_id"]); ?>
                                                <a href="<?php echo htmlspecialchars($tilhorighet_url); ?>">
                                                    <?php echo htmlspecialchars($tilhorende_oppgave["tittel"]); ?>
                                                </a>
                                            <?php else: ?>
                                                Hele gruppen
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="gruppe-handlinger">
                                                <?php $ressurs_url = url("/ressurs.php?ressurs_id=" . $ressurs["fil_id"]); ?>
                                                <a class="button button-primary" href="<?php echo htmlspecialchars($ressurs_url); ?>">
                                                    Åpne
                                                </a>

                                                <?php if ($tilstand["er_eier"]): ?>
                                                    <form method="post" action="" onsubmit="return confirm('Vil du slette denne ressursen?');">
                                                        <?php echo csrf_felt(); ?>
                                                        <input type="hidden" name="admin_handling" value="slett_ressurs">
                                                        <input type="hidden" name="fil_id" value="<?php echo (int) $ressurs["fil_id"]; ?>">
                                                        <button type="submit" class="button button-warning">
                                                            Slett
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>

                <div class="gruppe-opprett-omrade">
                    <h3>
                        Last opp ressurs
                    </h3>

                    <form method="post" action="" enctype="multipart/form-data" class="gruppe-last-opp">
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
                </div>
            </section>

        <?php elseif ($tilstand["section"] === "diskusjoner"): ?>
            <section>
                <div class="gruppe-section-heading">
                    <div>
                        <h2>Diskusjoner</h2>
                        <p>
                            Diskusjonstråder for gruppen.
                        </p>
                    </div>
                </div>

                <?php if (empty($tilstand["diskusjoner"])): ?>
                    <div class="gruppe-empty">
                        <h3>
                            Ingen diskusjoner ennå
                        </h3>
                    </div>
                <?php else: ?>
                    <ul class="gruppe-diskusjon-liste">
                        <?php foreach ($tilstand["diskusjoner"] as $trad): ?>
                            <li class="gruppe-diskusjon-admin">
                                <?php $diskusjon_url = url("/diskusjon.php?gruppe_id=" . $tilstand["gruppe"]["id"] . "&diskusjon_id=" . $trad["id"]); ?>
                                <a class="gruppe-diskusjon-kort" href="<?php echo htmlspecialchars($diskusjon_url); ?>">
                                    <span class="gruppe-diskusjon-tittel">
                                        <?php echo htmlspecialchars($trad["tittel"]); ?>
                                    </span>
                                    <span class="gruppe-diskusjon-meta">
                                        <?php echo (int) $trad["antall_innlegg"]; ?> innlegg
                                    </span>
                                </a>

                                <div class="gruppe-diskusjon-fot">
                                    <?php $tilhorende_oppgave = $trad["oppgave_id"] !== null ? ($tilstand["oppgave_etter_id"][(int) $trad["oppgave_id"]] ?? null) : null; ?>
                                    <?php $tilhorende_fil = $trad["fil_id"] !== null ? ($tilstand["ressurs_etter_id"][(int) $trad["fil_id"]] ?? null) : null; ?>

                                    <?php if ($tilhorende_oppgave !== null): ?>
                                        <?php $knytning_url = url("/oppgave.php?gruppe_id=" . $tilstand["gruppe"]["id"] . "&oppgave_id=" . $tilhorende_oppgave["oppgave_id"]); ?>
                                        <a class="gruppe-diskusjon-tag" href="<?php echo htmlspecialchars($knytning_url); ?>">
                                            Oppgave: <?php echo htmlspecialchars($tilhorende_oppgave["tittel"]); ?>
                                        </a>
                                    <?php elseif ($tilhorende_fil !== null): ?>
                                        <?php $knytning_url = url("/ressurs.php?ressurs_id=" . $tilhorende_fil["fil_id"]); ?>
                                        <a class="gruppe-diskusjon-tag" href="<?php echo htmlspecialchars($knytning_url); ?>">
                                            Fil: <?php echo htmlspecialchars($tilhorende_fil["fil_navn"]); ?>
                                        </a>
                                    <?php else: ?>
                                        <span class="gruppe-diskusjon-tag">
                                            Hele gruppen
                                        </span>
                                    <?php endif; ?>

                                    <?php if ($tilstand["er_eier"]): ?>
                                        <form method="post" action="" onsubmit="return confirm('Vil du slette denne diskusjonen og alle innleggene?');">
                                            <?php echo csrf_felt(); ?>
                                            <input type="hidden" name="admin_handling" value="slett_diskusjon">
                                            <input type="hidden" name="diskusjon_id" value="<?php echo (int) $trad["id"]; ?>">
                                            <button type="submit" class="button button-warning">
                                                Slett
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

                <div class="gruppe-opprett-omrade">
                    <h3>
                        Ny diskusjonstråd
                    </h3>

                    <form method="post" action="" class="form gruppe-ny-diskusjon">
                        <?php echo csrf_felt(); ?>

                        <div class="form-felt">
                            <label for="diskusjon_tittel">
                                Tittel
                            </label>
                            <input type="text" id="diskusjon_tittel" name="diskusjon_tittel" required>
                        </div>

                        <div class="form-felt">
                            <label for="diskusjon_oppgave_id">
                                Knytt til oppgave
                            </label>
                            <select id="diskusjon_oppgave_id" name="diskusjon_oppgave_id">
                                <option value="">Ingen</option>
                                <?php foreach ($tilstand["oppgaver"] as $oppgave): ?>
                                    <option value="<?php echo (int) $oppgave["oppgave_id"]; ?>">
                                        <?php echo htmlspecialchars($oppgave["tittel"]); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-felt">
                            <label for="diskusjon_fil_id">
                                Knytt til fil
                            </label>
                            <select id="diskusjon_fil_id" name="diskusjon_fil_id">
                                <option value="">Ingen</option>
                                <?php foreach ($tilstand["ressurser"] as $ressurs): ?>
                                    <option value="<?php echo (int) $ressurs["fil_id"]; ?>">
                                        <?php echo htmlspecialchars($ressurs["fil_navn"]); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <button type="submit" class="button button-primary">
                            Opprett diskusjonstråd
                        </button>
                    </form>
                </div>
            </section>
        <?php endif; ?>

    </div>

</section>
