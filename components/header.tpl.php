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


//$student_id = $_SESSION["student_id"];
$student_id = "1";

$student = hent_student($student_id);

?>

<nav class="navbar">
    <ul>
       <li><a href="/index.php"><strong>Studenthub</strong></a></li> 
    </ul>
    
    <ul>
        <li>
            <img src="<?php echo htmlspecialchars($student["avatar_link"]);?>" alt="Bruker Profile Bilde" /> 
        </li>
        <li>
            <span>Hei! <?php echo $student["fornavn"]; ?> <?php echo $student["etternavn"]; ?></span>
        </li>
        <li>
            <a href="/profil.php">Profil</a>
        </li>
        <li>
            <a href="/logout.php">Logg ut</a>
        </li>
    </ul>
</nav>
