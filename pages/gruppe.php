<?php

?>

<section>
    <div class="gruppe-title-row">
        <h1><?php echo $state["gruppe"]["navn"]; ?></h1>
<!--        TODO: Knapp til å endre navn på gruppe-->

        <div>
            <button type="button" class="button button-warning">Forlat Gruppe</button>
        </div>
    </div>

    <div class="gruppe-oppgaver-medlemmer">
        <section>
            <h2>Oppgaver</h2>

            <ul>
            <?php foreach ($state["oppgaver"] as $oppgave): ?>
                <li>
                    <article>
                        <h3><?php echo $oppgave["tittel"] ;?></h3>
                    </article>
                </li>
            <?php endforeach; ?>
            </ul>
        </section>

        <section>
            <h2>Medlemmer</h2>

            <ul>
            <?php foreach($state["medlemmer"] as $medlem): ?>
                <li>
                    <article class="gruppe-medlem">
                        <img class="gruppe-medlem-img" src="<?php echo htmlspecialchars($medlem["avatar_link"]);?>" alt="Bruker Profile Bilde" />
                        <span class="gruppe-medlem-navn"><?php echo $medlem["fornavn"] ;?> <?php echo $medlem["etternavn"] ;?></span>
                    </article>
                </li>
            <?php endforeach; ?>
            </ul>
        </section>
    </div>
</section>
