<section class="kalender">
    <h2 class="kalender-tittel">Kommende helligdager</h2>

    <?php if (empty($kommende)): ?>
        <p class="kalender-tom">Ingen kommende helligdager.</p>
    <?php else: ?>
        <ol class="kalender-liste">
            <?php foreach ($kommende as $dag): ?>
                <li class="kalender-element">
                    <div class="kalender-dato" aria-hidden="true">
                        <time class="kalender-dato-dag" datetime="<?php echo $dag["dato"]->format("Y-m-d"); ?>" data-format="dag">
                            <?php echo $dag["dato"]->format("j"); ?>
                        </time>
                        <time class="kalender-dato-maned" datetime="<?php echo $dag["dato"]->format("Y-m-d"); ?>" data-format="maned">
                            <?php echo $dag["dato"]->format("M"); ?>
                        </time>
                    </div>

                    <div class="kalender-info">
                        <span class="kalender-navn">
                            <?php echo htmlspecialchars($dag["navn"]); ?>
                        </span>
                        <time class="kalender-ukedag" datetime="<?php echo $dag["dato"]->format("Y-m-d"); ?>" data-format="full">
                            <?php echo $dag["dato"]->format("Y-m-d"); ?>
                        </time>
                    </div>
                </li>
            <?php endforeach; ?>
        </ol>
    <?php endif; ?>
</section>
