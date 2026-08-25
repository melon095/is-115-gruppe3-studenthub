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
        href="/gruppe.tpl.php?gruppe_id={$gruppeId}&section={$section}"
    >
        $str
    </a>
</li>
EOF;
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
            <h3>Ressurser</h3>

            <ul>
            <?php foreach($state["ressurser"] as $ressurs): ?>
                <li>
                    <p>
                        <?php echo $ressurs["fil_navn"] ;?>
                    </p>
                    <p>
                        <?php echo $ressurs["fil_størrelse"] ;?>
                    </p>
                    <section>
                        <h4>Revisjoner</h4>
                        <ol>
                        <?php foreach($ressurs["versjoner"] as $versjon): ?>
                            <li>
                                <article>
                                    <h5><?php echo $versjon["versjon_id"]; ?></h5>
                                    <p><?php echo $versjon["fil_lokasjon_hdd"]; ?></p>
                                </article>
                            </li>
                        <?php endforeach; ?>
                        </ol>
                    </section>
                </li>
            <?php endforeach; ?>
            </ul>
        </section>
    <?php endif; ?>
    </div>
</section>
