<section class="profil-side">
    <h1>Min profil</h1>

    <?php if (!empty($feil)): ?>
        <ul class="form-feil" role="alert">
            <?php foreach ($feil as $melding): ?>
                <li><?php echo htmlspecialchars($melding); ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <?php if (!empty($tilstand["suksess"])): ?>
        <p class="profil-suksess">Profilbildet ble oppdatert.</p>
    <?php endif; ?>

    <div class="profil-kort">
        <?php echo profilbilde_html($tilstand["student"], "profil-bilde", "profil-bilde-plassholder"); ?>

        <form method="post" action="" enctype="multipart/form-data" class="form">
            <?php echo csrf_felt(); ?>

            <div class="form-felt">
                <label for="avatar">Velg nytt profilbilde</label>
                <input
                    type="file"
                    id="avatar"
                    name="avatar"
                    accept="image/png,image/jpeg,image/gif,image/webp"
                    required>
            </div>

            <button type="submit" class="button button-primary">
                Last opp
            </button>
        </form>
    </div>
</section>
