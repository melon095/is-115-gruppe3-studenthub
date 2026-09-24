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
            <?php echo url("/registrer.php"); ?>Registrer deg</a>
        </p>

    </section>
</div>