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

        <?php
        echo '<form method="post" action="" class="form">';
        ?>

        <div class="form-felt">
            <label for="epost">E-post</label>

            <input
                type="email"
                id="epost"
                name="epost"
                autocomplete="email"
                value="<?php echo htmlspecialchars($epost ?? ""); ?>"
                required
            >
        </div>

        <div class="form-felt">
            <label for="passord">Passord</label>

            <input
                type="password"
                id="passord"
                name="passord"
                autocomplete="current-password"
                required
            >
        </div>

        <?php
        echo '<button type="submit" class="button button-primary button-lg">';
        echo 'Logg inn';
        echo '</button>';

        echo '</form>';
        ?>

        <p class="auth-lenke">
            Ikke registrert?

            <?php
            echo '<a href="' . htmlspecialchars(url("/registrer.php")) . '">';
            echo 'Registrer deg';
            echo '</a>';
            ?>
        </p>

    </section>
</div>