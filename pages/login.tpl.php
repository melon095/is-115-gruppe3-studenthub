<?php

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    // TODO: Database integrasjon.
    // TODO: Validere bruker input.
    $_SESSION['student_id'] = 1;
    header('Location: index.php');
    exit();
}

?>

<div class="auth-wrapper">
    <section class="auth-card" aria-labelledby="login-heading">
        <h1 id="login-heading">Logg inn</h1>

        <form method="post" action="" class="form">
            <div class="form-felt">
                <label for="epost">E-post</label>
                <input type="email" id="epost" name="epost" autocomplete="email" required>
            </div>

            <div class="form-felt">
                <label for="passord">Passord</label>
                <input type="password" id="passord" name="passord" autocomplete="current-password" required>
            </div>

            <button type="submit" class="button button-primary button-lg">Logg inn</button>
        </form>

        <p class="auth-lenke">
            Ikke registrert? <a href="/registrer.php">Registrer deg</a>
        </p>
    </section>
</div>
