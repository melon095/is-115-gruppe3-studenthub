<section class="profil-side">
    <h1>Min profil</h1>

    <?php if (!empty($feil)): ?>
        <ul class="form-feil" role="alert">
            <?php foreach ($feil as $melding): ?>
                <li><?php echo htmlspecialchars($melding); ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <?php if ($tilstand["suksess"] === "avatar"): ?>
        <p class="profil-suksess">Profilbildet ble oppdatert.</p>
    <?php elseif ($tilstand["suksess"] === "passord"): ?>
        <p class="profil-suksess">Passordet ble endret.</p>
    <?php endif; ?>

    <section class="profil-kort">
        <h2>Informasjon i Databasen</h2>
        <table class="profil-tabell">
            <tbody>
                <tr>
                    <th scope="row">ID</th>
                    <td><?php echo (int) $tilstand["student"]["id"]; ?></td>
                </tr>
                <tr>
                    <th scope="row">Fornavn</th>
                    <td><?php echo htmlspecialchars($tilstand["student"]["fornavn"]); ?></td>
                </tr>
                <tr>
                    <th scope="row">Etternavn</th>
                    <td><?php echo htmlspecialchars($tilstand["student"]["etternavn"]); ?></td>
                </tr>
                <tr>
                    <th scope="row">E-post</th>
                    <td><?php echo htmlspecialchars($tilstand["student"]["epost"]); ?></td>
                </tr>
                <tr>
                    <th scope="row">Mobilnummer</th>
                    <td><?php echo htmlspecialchars($tilstand["student"]["mobilnummer"]); ?></td>
                </tr>
                <tr>
                    <th scope="row">Opprettet</th>
                    <td><?php echo htmlspecialchars($tilstand["student"]["opprettet_på"]); ?></td>
                </tr>
            </tbody>
        </table>
    </section>


    <div class="profil-kort">
        <?php echo profilbilde_html($tilstand["student"], "profil-bilde", "profil-bilde-plassholder"); ?>

        <form method="post" action="" enctype="multipart/form-data" class="form">
            <?php echo csrf_felt(); ?>
            <input type="hidden" name="handling" value="avatar">

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

    <div class="profil-kort">
        <h2>Endre passord</h2>

        <form method="post" action="" class="form">
            <?php echo csrf_felt(); ?>
            <input type="hidden" name="handling" value="passord">

            <div class="form-felt">
                <label for="gammelt_passord">Nåværende passord</label>
                <input
                    type="password"
                    id="gammelt_passord"
                    name="gammelt_passord"
                    autocomplete="current-password"
                    required>
            </div>

            <div class="form-felt">
                <label for="nytt_passord">Nytt passord</label>
                <input
                    type="password"
                    id="nytt_passord"
                    name="nytt_passord"
                    autocomplete="new-password"
                    minlength="8"
                    aria-describedby="nytt-passord-krav"
                    required>
                <small id="nytt-passord-krav">Minst 8 tegn, én stor bokstav og ett spesialtegn.</small>
            </div>

            <div class="form-felt">
                <label for="bekreft_nytt_passord">Bekreft nytt passord</label>
                <input
                    type="password"
                    id="bekreft_nytt_passord"
                    name="bekreft_nytt_passord"
                    autocomplete="new-password"
                    required>
            </div>

            <button type="submit" class="button button-primary">
                Endre passord
            </button>
        </form>
    </div>
</section>
