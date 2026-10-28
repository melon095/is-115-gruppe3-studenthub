<section>
    <h1>
        Inviter til
        <?php echo htmlspecialchars($tilstand["gruppe"]["navn"]); ?>
    </h1>

    <?php if (!empty($feil)): ?>
        <ul class="inviter-feil" role="alert">
            <?php foreach ($feil as $melding): ?>
                <li><?php echo htmlspecialchars($melding); ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <?php if ($tilstand["sendt_til"] !== null): ?>
        <div class="card inviter-resultat">
            <p>
                Invitasjonen ble sendt til <strong><?php echo htmlspecialchars($tilstand["sendt_til"]); ?></strong>.
            </p>
        </div>
    <?php endif; ?>

    <?php if ($generert_lenke !== null): ?>
        <div class="card inviter-resultat">
            <h2>Invitasjonslenke</h2>
            <p>
                Send denne lenken til personen du vil invitere.
            </p>

            <div class="inviter-lenke-rad">
                <input type="text" id="invitasjonslenke" value="<?php echo htmlspecialchars($generert_lenke); ?>" readonly>
                <button type="button" class="button button-secondary" data-kopier-mal="invitasjonslenke">
                    Kopier
                </button>
            </div>
        </div>
    <?php endif; ?>

    <div class="inviter-metoder">
        <section class="card">
            <h2>Inviter via e-post</h2>
            <p>
                Skriv inn e-postadressen til personen du vil invitere, og vi sender en invitasjon direkte.
            </p>

            <form method="post" action="" class="form">
                <?php echo csrf_felt(); ?>
                <input type="hidden" name="handling" value="send_epost">

                <div class="form-felt">
                    <label for="epost">
                        E-postadresse
                    </label>
                    <input type="email" id="epost" name="epost" required>
                </div>

                <button type="submit" class="button button-primary">
                    Send invitasjon
                </button>
            </form>
        </section>

        <section class="card">
            <h2>Inviter via lenke</h2>
            <p>
                Generer en unik lenke og send den til personen du vil invitere.
            </p>

            <form method="post" action="" class="form">
                <?php echo csrf_felt(); ?>
                <input type="hidden" name="handling" value="generer_lenke">

                <button type="submit" class="button button-secondary">
                    Generer invitasjonslenke
                </button>
            </form>
        </section>
    </div>
</section>
