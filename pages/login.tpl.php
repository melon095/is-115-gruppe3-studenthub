<?php

$feil = [];
$epost = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $input = filter_input_array(INPUT_POST, [
        "epost" => FILTER_VALIDATE_EMAIL,
        "passord" => FILTER_DEFAULT
    ]);

    $epost = $input["epost"] ?? false;
    $passord = $input["passord"] ?? "";

    if (!$epost || $passord === "") {
        $feil[] = "Ugyldig e-post eller passord.";
    } else {
        $stmt = $pdo->prepare("
            SELECT id, passord
            FROM studenter
            WHERE epost = :epost
        ");

        $stmt->execute([
            "epost" => $epost
        ]);

        $student = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($student && password_verify($passord, $student["passord"])) {
            $_SESSION["student_id"] = $student["id"];

            header("Location: " . url("/index.php"));
            exit();
        }

        $feil[] = "Feil e-post eller passord.";
    }
}

?>

<div class="auth-wrapper">
    <section class="auth-card" aria-labelledby="login-heading">

        <h1 id="login-heading">Logg inn</h1>

        <?php if (!empty($feil)): ?>
            <ul class="form-feil" role="alert">
                <?php foreach ($feil as $melding): ?>
                    <li><?php echo htmlspecialchars($melding); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        

        <p class="auth-lenke">
            Ikke registrert?
            <?php echo url('/registrer.php'); ?>Registrer deg</a>
``
        </p>

    </section>
</div>