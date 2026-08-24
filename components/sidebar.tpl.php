<?php

function hent_student_grupper(string $student_id): array {
    $arr = [];

    for ($i = 1; $i < 51; $i++) {
        $arr[] = [
            "gruppe_id"=> $i,
            "navn" => "Gruppe " . $i
        ];
    }

    return $arr;
}

$student_id = "1";

$grupper = hent_student_grupper($student_id);
$current_gruppe_id = $_GET["gruppe_id"] ?? null;

?>

<aside class="sidebar" id="sidebar">
    <nav class="grupper-nav">
        <section>
            <h2>Grupper du er i</h2>
            <ul class="grupper-liste">
            <?php foreach($grupper as $gruppe ):?>
                <li>
                    <a 
                        href="/gruppe.php?gruppe_id=<?php echo $gruppe["gruppe_id"]; ?>"
                        <?php echo ($current_gruppe_id == $gruppe["gruppe_id"]) ? 'aria-current="page"' : ''; ?>
                    >
                        <?php echo htmlspecialchars($gruppe["navn"]); ?>
                    </a>
                </li>
            <?php endforeach; ?>
            </ul>
        </section>
    </nav>
</aside>
