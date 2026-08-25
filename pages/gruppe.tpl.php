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
        <h1><?php echo $state["gruppe"]["navn"]; ?></h1>
<!--        TODO: Knapp for å endre navn på gruppe-->

        <div>
            <button type="button" class="button button-warning">Forlat Gruppe</button>
        </div>
    </div>
    
    <div>
        <ul class="gruppe-section-list">
            <?php echo section_btn($state, "oppgaver", "Oppgaver"); ?>
            <?php echo section_btn($state, "medlemmer", "Medlemmer"); ?>
            <?php echo section_btn($state, "ressurser", "Ressurser"); ?>
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
                                <h3><?php echo $oppgave["tittel"] ;?></h3>
                                <p><?php echo $oppgave["beskrivelse"] ;?></p>
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
        </section>

    <?php elseif ($state["section"] == "medlemmer"): ?>
        <section>
            <h2>Medlemmer</h2>

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
                            <th scope="row"><?php echo $ressurs["fil_navn"] ;?></th>
                            <td><?php echo $ressurs["fil_type"]; ?></td>
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
        </section>
    <?php endif; ?>
    </div>
</section>
