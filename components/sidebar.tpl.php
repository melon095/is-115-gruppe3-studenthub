<?php

$grupper = [];

if (isset($_SESSION["student_id"])) {
    $stmt = $pdo->prepare("
        SELECT
            g.id AS gruppe_id,
            g.navn
        FROM grupper g
        INNER JOIN gruppe_medlemmer gm
            ON gm.gruppe_id = g.id
        WHERE gm.bruker_id = :student_id
        ORDER BY g.navn
    ");

    $stmt->execute([
        "student_id" => $_SESSION["student_id"]
    ]);

    $grupper = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$current_gruppe_id = filter_input(
    INPUT_GET,
    "gruppe_id",
    FILTER_VALIDATE_INT
);

?>

<?php if (isset($_SESSION["student_id"])): ?>

<aside class="sidebar" id="sidebar">
    <nav class="grupper-nav">
        <section>

            <h2>Grupper du er medlem av</h2>

            <?php if (empty($grupper)): ?>

                <p>Du er ikke medlem av noen grupper ennå.</p>

            <?php else: ?>

                <ul class="grupper-liste">

                    <?php foreach ($grupper as $gruppe): ?>

                        <li>
                            <a href="<?php echo url(
                                "gruppe.php?gruppe_id=" . (int) $gruppe["gruppe_id"]
                            ); ?>"
                                <?php
                                if (
                                    (int) $current_gruppe_id ===
                                    (int) $gruppe["gruppe_id"]
                                ) {
                                    echo 'aria-current="page"';
                                }
                                ?>
                            >
                                <?php echo htmlspecialchars($gruppe["navn"]); ?>
                            </a>
                        </li>

                    <?php endforeach; ?>

                </ul>

            <?php endif; ?>

        </section>
    </nav>
</aside>

<?php endif; ?>
