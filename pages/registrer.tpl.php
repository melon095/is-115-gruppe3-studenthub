<?php

$feil = [];

$fornavn = "";
$etternavn = "";
$epost = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = filter_input_array(INPUT_POST, [
        'fornavn' => FILTER_DEFAULT,
        'etternavn' => FILTER_DEFAULT,
        'epost' => FILTER_VALIDATE_EMAIL,
        'passord' => FILTER_DEFAULT,
        'bekreft_passord' => FILTER_DEFAULT
    ]);

    $fornavn = trim($input['fornavn'] ?? '');
    $etternavn = trim($input['etternavn'] ?? '');
    $epost = $input['epost'] ?? false;
    $passord = $input['passord'] ?? '';
    $bekreft_passord = $input['bekreft_passord'] ?? '';

    if ($fornavn === '') {
        $feil[] = "Fornavn må fylles ut.";
    }

    if ($etternavn === '') {
        $feil[] = "Etternavn må fylles ut.";
    }

    if (!$epost) {
        $feil[] = "Du må skrive inn en gyldig e-postadresse.";
    }

    if ($passord === '') {
        $feil[] = "Passord må fylles ut.";
    }

    if ($passord !== $bekreft_passord) {
        $feil[] = "Passordene er ikke like.";
    }

    if (empty($feil)) {
        $stmt = $pdo->prepare("
            SELECT id
            FROM studenter
            WHERE epost = :epost
        ");

        $stmt->execute([
            'epost' => $epost
        ]);

        if ($stmt->fetch()) {
            $feil[] = "Det finnes allerede en bruker med denne e-postadressen.";
        } else {
            $passord_hash = password_hash($passord, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare("
                INSERT INTO studenter (fornavn, etternavn, epost, passord)
                VALUES (:fornavn, :etternavn, :epost, :passord)
            ");

            $stmt->execute([
                'fornavn' => $fornavn,
                'etternavn' => $etternavn,
                'epost' => $epost,
                'passord' => $passord_hash
            ]);

            $_SESSION['student_id'] = $pdo->lastInsertId();

            header('Location: ' . url('/index.php'));
            exit();
        }
    }
}

?>

<div class="auth-wrapper">
    <section class="auth-card" aria-labelledby="registrer-heading">
        <h1 id="registrer-heading">Registrer deg</h1>

        <?php if (!empty($feil)): ?>
            <ul class="form-feil" role="alert">
                <?php foreach ($feil as $melding): ?>
                    <li><?php echo htmlspecialchars($melding); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        

        <p class="auth-lenke">
            Har du allerede en konto?
            <?php echo url('/login.php'); ?>Logg inn</a>
        </p>
    </section>
</div>