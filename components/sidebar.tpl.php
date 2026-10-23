<?php

$grupper = [];

$vis_sidebar = !($offentlig_side ?? false) && isset($_SESSION["student_id"]);

if ($vis_sidebar) {
    $grupper = database_hent_grupper_for_student($pdo, $_SESSION["student_id"]);
}

$nåværende_gruppe_id = filter_input(
    INPUT_GET,
    "gruppe_id",
    FILTER_VALIDATE_INT
);

?>

<?php if ($vis_sidebar): ?>
    <aside class="sidebar" id="sidebar">
        <nav class="grupper-nav">
            <section>
                <h2>Grupper du er medlem av</h2>
                <?php if (empty($grupper)): ?>
                    <p>
                        Du er ikke medlem av noen grupper ennå.
                    </p>
                <?php else: ?>
                    <ul class="grupper-liste">
                        <?php foreach ($grupper as $gruppe): ?>
                            <li>
                                <a
                                    href="<?php echo url('/gruppe.php?gruppe_id=' . $gruppe['gruppe_id'] . '&section=oppgaver'); ?>"
                                    <?php echo ($nåværende_gruppe_id == $gruppe['gruppe_id'] ? 'aria-current="page"' : ''); ?>>
                                    <?php echo htmlspecialchars($gruppe['navn']); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </section>
        </nav>
    </aside>
<?php endif; ?>
