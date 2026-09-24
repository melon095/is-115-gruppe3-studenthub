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
            <?php echo url("/"); ?>">
                <strong>Studenthub</strong>
            </a>
        </li>
    </ul>

    <ul>
        <?php if ($student !== null): ?>

            <?php if (!empty($student["avatar_link"])): ?>
                <li>
                    <?php echo url("profil.php"); ?>"
                        class="navbar-avatar-link"
                    >
                        <?php echo htmlspecialchars($student["avatar_link"]); ?>"
                            alt="Brukerprofilbilde"
                        >
                    </a>
                </li>
            <?php endif; ?>

            <li class="navbar-hilsen">
                <span>
                    Hei!
                    <?php echo htmlspecialchars($student["fornavn"]); ?>
                    <?php echo htmlspecialchars($student["etternavn"]); ?>
                </span>
            </li>

            <li>
                <?php echo url("logg-ut.php"); ?>">
                    Logg ut
                </a>
            </li>

        <?php else: ?>

            <li>
                <?php echo url("logg-inn.php"); ?>">
                    Logg inn
                </a>
            </li>

            <li>
                <?php echo url("registrer.php"); ?>">
                    Registrer
                </a>
            </li>

        <?php endif; ?>
    </ul>
</nav>
