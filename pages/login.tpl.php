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

        <form method="post" action="" class="form">

            <div class="form-felt">
                <label for="epost">E-post</label>
                <input
                    type="email"
                    id="epost"
                    name="epost"
                    autocomplete="email"
                    value="<?php echo htmlspecialchars($epost ?? ''); ?>"
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

            <button type="submit" class="button button-primary button-lg">
                Logg inn
            </button>

        </form>

        <p class="auth-lenke">
        