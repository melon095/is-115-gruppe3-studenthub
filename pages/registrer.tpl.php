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
            <?php echo url("/login.php"); ?>Logg inn</a>
        </p>

    </section>
</div>