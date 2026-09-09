<?php

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    // TODO: Database integrasjon.
    // TODO: Validere bruker input.
    $_SESSION['student_id'] = 1;
    header('Location: ' . url('/index.php'));
    exit();
}

?>

<div class="auth-wrapper">
    <section class="auth-card" aria-labelledby="registrer-heading">
        <h1 id="registrer-heading">Registrer deg</h1>

        <form method="post" action="" class="form">
            <div class="form-felt">
                <label for="fornavn">Fornavn</label>
                <input type="text" id="fornavn" name="fornavn" autocomplete="given-name" required>
            </div>

            <div class="form-felt">
                <label for="etternavn">Etternavn</label>
                <input type="text" id="etternavn" name="etternavn" autocomplete="family-name" required>
            </div>

            <div class="form-felt">
                <label for="epost">E-post</label>
                <input type="email" id="epost" name="epost" autocomplete="email" required>
            </div>

            <div class="form-felt">
                <label for="passord">Passord</label>
                <input type="password" id="passord" name="passord" autocomplete="new-password" required>
            </div>

            <div class="form-felt">
                <label for="bekreft_passord">Bekreft passord</label>
                <input type="password" id="bekreft_passord" name="bekreft_passord" autocomplete="new-password" required>
            </div>

            <button type="submit" class="button button-primary button-lg">Registrer deg</button>
        </form>

        <p class="auth-lenke">
            Har du allerede en konto? <a href="<?php echo url("/login.php"); ?>">Logg inn</a>
        </p>
    </section>
</div>
