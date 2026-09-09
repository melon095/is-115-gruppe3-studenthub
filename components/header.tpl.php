<?php

function hent_student(string $student_id): array  {
    return [
        "student_id" => $student_id,
        "fornavn" => "Kai",
        "etternavn"=> "Eide",
        "epost" => "kai@eide.no",
        "avatar_link" => "http://dummyimage.com/172x100.png/dddddd/000000"
    ];
}

?>

<nav class="navbar">
    <button id="toggle-sidebar" class="toggle-btn" aria-label="Åpne meny" aria-controls="sidebar">☰</button>

    <ul>
       <li><a href="<?php echo url("/index.php"); ?>"><strong>Studenthub</strong></a></li>
    </ul>

    <ul>
    <?php if (isset($_SESSION['student_id'])): ?>
    <?php
        $student_id = $_SESSION['student_id'];
        $student = hent_student($student_id);
    ?>
        <li>
            <a href="<?php echo url("/profil.php"); ?>" class="navbar-avatar-link">
                <img src="<?php echo htmlspecialchars($student["avatar_link"]);?>" alt="Bruker Profile Bilde" />
            </a>
        </li>
        <li class="navbar-hilsen">
            <span>Hei! <?php echo $student["fornavn"]; ?> <?php echo $student["etternavn"]; ?></span>
        </li>
        <li>
            <a href="<?php echo url("/logout.php"); ?>">Logg ut</a>
        </li>
    <?php else: ?>
        <li>
            <a href="/login.php">Logg inn</a>
        </li>
        <li>
            <a href="/registrer.php">Registrer</a>
        </li>
    <?php endif; ?>
    </ul>
</nav>
