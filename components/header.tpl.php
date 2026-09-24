<?php

$student = null;

if (isset($_SESSION["student_id"])) {
    $stmt = $pdo->prepare("
        SELECT id, fornavn, etternavn, epost, avatar_link
        FROM studenter
        WHERE id = :student_id
    ");

    $stmt->execute([
        "student_id" => $_SESSION["student_id"]
    ]);

    $student = $stmt->fetch(PDO::FETCH_ASSOC);
}

?>

<nav class="navbar">

    <button
        id="toggle-sidebar"
        class="toggle-btn"
        aria-label="Åpne meny"
        aria-controls="sidebar"
    >
        ☰
    </button>

    <ul>
        <li>
            <?php
            echo '<a href="' . htmlspecialchars(url("/index.php")) . '">';
            ?>
            <strong>Studenthub</strong>
            <?php echo '</a>'; ?>
        </li>
    </ul>

    <ul>
        <?php if ($student): ?>

            <li class="navbar-hilsen">
                <span>
                    Hei!
                    <?php echo htmlspecialchars($student["fornavn"]); ?>
                    <?php echo htmlspecialchars($student["etternavn"]); ?>
                </span>
            </li>

            <li>
                <?php
                echo '<a href="' . htmlspecialchars(url("/logout.php")) . '">';
                echo 'Logg ut';
                echo '</a>';
                ?>
            </li>

        <?php else: ?>

            <li>
                <?php
                echo '<a href="' . htmlspecialchars(url("/login.php")) . '">';
                echo 'Logg inn';
                echo '</a>';
                ?>
            </li>

            <li>
                <?php
                echo '<a href="' . htmlspecialchars(url("/registrer.php")) . '">';
                echo 'Registrer';
                echo '</a>';
                ?>
            </li>

        <?php endif; ?>
    </ul>

</nav>