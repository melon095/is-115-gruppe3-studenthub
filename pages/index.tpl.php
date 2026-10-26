<section>
    <div class="index-title-row">
        <h1>Hjem</h1>
        <a href="<?php echo url("/opprett-gruppe.php"); ?>" class="button button-primary">Opprett gruppe</a>
    </div>

    <h2>Alle grupper</h2>
    <p class="alle-grupper-info">
        En oversikt over alle grupper på plattformen.
    </p>

    <?php if (empty($tilstand["alle_grupper"])): ?>
        <p>Det finnes ingen grupper på plattformen ennå.</p>
    <?php else: ?>
        <ul class="alle-grupper-liste">
            <?php foreach ($tilstand["alle_grupper"] as $gruppe): ?>
                <li class="gruppe-kort">
                    <?php if ($gruppe["er_medlem"]): ?>
                        <span class="gruppe-kort-badge">Du er medlem av</span>
                    <?php endif; ?>

                    <h3><?php echo htmlspecialchars($gruppe["navn"]); ?></h3>

                    <?php if (!empty($gruppe["beskrivelse"])): ?>
                        <p class="gruppe-kort-beskrivelse"><?php echo htmlspecialchars($gruppe["beskrivelse"]); ?></p>
                    <?php endif; ?>

                    <?php if (!empty($gruppe["medlemmer"])): ?>
                        <?php
                        $synlige_medlemmer = array_slice($gruppe["medlemmer"], 0, 5);
                        $resterende = count($gruppe["medlemmer"]) - count($synlige_medlemmer);
                        ?>
                        <details class="gruppe-kort-medlemmer">
                            <summary class="gruppe-avatar-stack">
                                <?php foreach ($synlige_medlemmer as $medlem): ?>
                                    <?php $medlem_navn = fullt_navn($medlem); ?>
                                    <?php if (!empty($medlem["avatar_link"])): ?>
                                        <img
                                            class="gruppe-avatar"
                                            src="<?php echo htmlspecialchars($medlem["avatar_link"]); ?>"
                                            alt="<?php echo htmlspecialchars($medlem_navn); ?>"
                                            title="<?php echo htmlspecialchars($medlem_navn); ?>">
                                    <?php else: ?>
                                        <span class="gruppe-avatar gruppe-avatar-placeholder" title="<?php echo htmlspecialchars($medlem_navn); ?>">
                                            <?php echo htmlspecialchars(forbokstav($medlem["fornavn"])); ?>
                                        </span>
                                    <?php endif; ?>
                                <?php endforeach; ?>

                                <?php if ($resterende > 0): ?>
                                    <span class="gruppe-avatar gruppe-avatar-mer">
                                        +<?php echo $resterende; ?>
                                    </span>
                                <?php endif; ?>
                            </summary>

                            <ul class="gruppe-medlemsliste">
                                <?php foreach ($gruppe["medlemmer"] as $medlem): ?>
                                    <li><?php echo htmlspecialchars(fullt_navn($medlem)); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </details>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</section>
