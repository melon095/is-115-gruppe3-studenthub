<?php

$student = null;

if (isset($_SESSION["student_id"])) {
    $student = database_hent_student($pdo, $_SESSION["student_id"]);
}

?>

<nav class="navbar">

    <button
        id="toggle-sidebar"
        class="toggle-btn"
        aria-label="Åpne meny"
        aria-controls="sidebar">
        ☰
    </button>

    <ul>
        <li>
            <a href="<?php echo htmlspecialchars(url('/index.php')); ?>">
                <strong>Studenthub</strong>
            </a>
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
                <a class="navbar-avatar-link" href="<?php echo htmlspecialchars(url('/profil.php')); ?>">
                    <?php echo profilbilde_html($student, "navbar-avatar", "navbar-avatar-placeholder"); ?>
                </a>
            </li>

            <li>
                <a href="<?php echo htmlspecialchars(url('/logout.php')); ?>">
                    Logg ut
                </a>
            </li>
        <?php else: ?>
            <li>
                <a href="<?php echo htmlspecialchars(url('/login.php')); ?>">
                    Logg inn
                </a>
            </li>
            <li>
                <a href="<?php echo htmlspecialchars(url('/registrer.php')); ?>">
                    Registrer
                </a>
            </li>
        <?php endif; ?>
    </ul>

</nav>
