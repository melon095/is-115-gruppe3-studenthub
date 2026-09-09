<?php

function section_btn(array $state, string $section, string $str) {
    $btnClass = $state["section"] === $section
            ? 'primary'
            : 'secondary';
    
    $gruppeId = $state["gruppe"]["id"];
    
    return <<<EOF
<li>
    <a 
        class="button button-{$btnClass}" 
        href="/gruppe.php?gruppe_id={$gruppeId}&section={$section}"
    >
        $str
    </a>
</li>
EOF;
}

// https://stackoverflow.com/a/28047922
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
    <div class="gruppe-title-row">
        <h1><?php echo htmlspecialchars($state["gruppe"]["navn"]); ?></h1>

        <div class="gruppe-title-knapper">
            <details class="gruppe-endre-navn">
                <summary class="button button-secondary">Endre navn</summary>

                <form method="post" action="" class="form">
                    <div class="form-felt">
                        <label for="gruppe_navn">Nytt navn</label>
                        <input type="text" id="gruppe_navn" name="gruppe_navn" value="<?php echo htmlspecialchars($state["gruppe"]["navn"]); ?>" required>
                    </div>

                    <button type="submit" class="button button-primary">Lagre</button>
                </form>
            </details>

            <a href="/forlat-gruppe.php?gruppe_id=<?php echo $state["gruppe"]["id"]; ?>" class="button button-warning">Forlat Gruppe</a>
        </div>
    </div>
    
    <div>
        <ul class="gruppe-section-list">
            <?php echo section_btn($state, "oppgaver", "Oppgaver"); ?>
            <?php echo section_btn($state, "medlemmer", "Medlemmer"); ?>
            <?php echo section_btn($state, "ressurser", "Ressurser"); ?>
            <?php echo section_btn($state, "diskusjoner", "Diskusjoner"); ?>
        </ul>
    </div>
    <div class="gruppe-sections">
    <?php if ($state["section"] == "oppgaver"): ?>
        <section>
            <h2>Oppgaver</h2>

            <ul>
                <?php foreach ($state["oppgaver"] as $oppgave): ?>
                    <li>
                        <article class="gruppe-oppgaver-oppgave">
                            <div>
                                <h3><?php echo htmlspecialchars($oppgave["tittel"]) ;?></h3>
                                <p><?php echo htmlspecialchars($oppgave["beskrivelse"]) ;?></p>
                            </div>

                            <div>
                                <a
                                    class="button button-primary"
                                    href="/oppgave.php?gruppe_id=<?php echo $state["gruppe"]["id"] ;?>&oppgave_id=<?php echo $oppgave["oppgave_id"] ;?>"
                                >
                                    Åpne
                                </a>
                            </div>
                        </article>
                    </li>
                <?php endforeach; ?>
            </ul>

            <form method="post" action="" class="form gruppe-ny-oppgave">
                <div class="form-felt">
                    <label for="oppgave_tittel">Tittel</label>
                    <input type="text" id="oppgave_tittel" name="oppgave_tittel" required>
                </div>

                <div class="form-felt">
                    <label for="oppgave_beskrivelse">Beskrivelse (valgfritt)</label>
                    <textarea id="oppgave_beskrivelse" name="oppgave_beskrivelse" rows="3"></textarea>
                </div>

                <button type="submit" class="button button-primary">Opprett oppgave</button>
            </form>
        </section>

    <?php elseif ($state["section"] == "medlemmer"): ?>
        <section>
            <div class="gruppe-medlemmer-title-row">
                <h2>Medlemmer</h2>
                <a href="/inviter.php?gruppe_id=<?php echo $state["gruppe"]["id"]; ?>" class="button button-primary">Inviter medlem</a>
            </div>

            <ul>
                <?php foreach($state["medlemmer"] as $medlem): ?>
                    <li class="gruppe-medlem">
                        <img class="gruppe-medlem-img" src="<?php echo htmlspecialchars($medlem["avatar_link"]);?>" alt="Bruker Profile Bilde" />
                        <span class="gruppe-medlem-navn"><?php echo $medlem["fornavn"] ;?> <?php echo $medlem["etternavn"] ;?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>
    <?php elseif ($state["section"] == "ressurser"): ?>
        <section>
            <h2>Ressurser</h2>

            <div class="ressurs-tabell-wrapper">
                <table class="ressurs-tabell">
                    <caption>Ressurser som er designert global i gruppen.</caption>
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
                    <?php foreach($state["ressurser"] as $ressurs): ?>
                        <tr>
                            <th scope="row"><?php echo htmlspecialchars($ressurs["fil_navn"]) ;?></th>
                            <td><?php echo htmlspecialchars($ressurs["fil_type"]); ?></td>
                            <td><?php echo bytes_til_menneske($ressurs["fil_størrelse"]); ?></td>
                            <td><?php echo $ressurs["siste_versjon"]["versjon_nummer"] ; ?></td>
                            <td>
                                <a
                                    role="button"
                                    class="button button-primary"
                                    href="/ressurs.php?ressurs_id=<?php echo $ressurs["fil_id"] ;?>"
                                >
                                    Åpne
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <form method="post" action="" enctype="multipart/form-data" class="gruppe-last-opp">
                <div class="form-felt">
                    <label for="ny_fil">Last opp fil til gruppen</label>
                    <input type="file" id="ny_fil" name="ny_fil" required>
                </div>

                <button type="submit" class="button button-primary">Last opp</button>
            </form>
        </section>
    <?php elseif ($state["section"] == "diskusjoner"): ?>
        <section>
            <h2>Diskusjoner</h2>

            <?php if ($state["filter_oppgave"] !== null || $state["filter_fil"] !== null): ?>
                <p class="gruppe-diskusjon-filter">
                    Viser diskusjoner merket med
                    <?php if ($state["filter_oppgave"] !== null): ?>
                        oppgaven "<?php echo htmlspecialchars($state["filter_oppgave"]["tittel"]); ?>"
                    <?php else: ?>
                        filen "<?php echo htmlspecialchars($state["filter_fil"]["fil_navn"]); ?>"
                    <?php endif; ?>
                    <a href="/gruppe.php?gruppe_id=<?php echo $state["gruppe"]["id"]; ?>&section=diskusjoner">Vis alle</a>
                </p>
            <?php endif; ?>

            <ul class="gruppe-diskusjon-liste">
                <?php foreach ($state["diskusjoner_visning"] as $trad): ?>
                    <li>
                        <a
                            class="gruppe-diskusjon-kort"
                            href="/diskusjon.php?gruppe_id=<?php echo $state["gruppe"]["id"]; ?>&diskusjon_id=<?php echo $trad["id"]; ?><?php echo $trad["oppgave_id"] !== null ? "&oppgave_id=" . $trad["oppgave_id"] : ""; ?><?php echo $trad["fil_id"] !== null ? "&fil_id=" . $trad["fil_id"] : ""; ?>"
                        >
                            <span class="gruppe-diskusjon-tittel"><?php echo htmlspecialchars($trad["tittel"]); ?></span>
                            <span class="gruppe-diskusjon-meta">
                                <?php echo (int) $trad["antall_innlegg"]; ?> innlegg sist aktiv <?php echo htmlspecialchars($trad["siste_aktivitet"]); ?>
                                <?php
                                    // TODO: Inkludere navn på oppgave eller fil når databaseintegrasjon er gjort.
                                ?>
                                <?php if ($trad["oppgave_id"] !== null): ?>
                                    <span class="gruppe-diskusjon-tag">Oppgave <?php echo htmlspecialchars($trad["oppgave_id"]); ?></span>
                                <?php elseif ($trad["fil_id"] !== null): ?>
                                    <span class="gruppe-diskusjon-tag">Fil <?php echo htmlspecialchars($trad["fil_id"]); ?></span>
                                <?php endif; ?>
                            </span>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>

            <form method="post" action="" class="form gruppe-ny-diskusjon">
                <div class="form-felt">
                    <label for="diskusjon_tittel">Ny diskusjonstråd</label>
                    <input type="text" id="diskusjon_tittel" name="diskusjon_tittel" placeholder="F.eks. Spørsmål om innlevering" required>
                </div>

                <div class="form-felt">
                    <label for="diskusjon_oppgave_id">Knytt til oppgave (valgfritt)</label>
                    <select id="diskusjon_oppgave_id" name="diskusjon_oppgave_id">
                        <option value="">Ingen</option>
                        <?php foreach ($state["oppgaver"] as $oppgave): ?>
                            <option value="<?php echo $oppgave["oppgave_id"]; ?>"><?php echo htmlspecialchars($oppgave["tittel"]); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-felt">
                    <label for="diskusjon_fil_id">Knytt til fil (valgfritt)</label>
                    <select id="diskusjon_fil_id" name="diskusjon_fil_id">
                        <option value="">Ingen</option>
                        <?php foreach ($state["ressurser"] as $ressurs): ?>
                            <option value="<?php echo $ressurs["fil_id"]; ?>"><?php echo htmlspecialchars($ressurs["fil_navn"]); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="submit" class="button button-primary">Opprett diskusjonstråd</button>
            </form>
        </section>
    <?php endif; ?>
    </div>
</section>
