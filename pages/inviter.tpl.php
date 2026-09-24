<section>

    <h1>
        Inviter til
        <?php echo htmlspecialchars($state["gruppe"]["navn"]); ?>
    </h1>

    <?php if (!empty($feil)): ?>

        <ul class="inviter-feil" role="alert">

            <?php foreach ($feil as $melding): ?>

                <li>
                    <?php echo htmlspecialchars($melding); ?>
                </li>

            <?php endforeach; ?>

        </ul>

    <?php endif; ?>


    <?php if ($generert_lenke !== null): ?>

        <div class="card inviter-resultat">

            <h2>Invitasjonslenke</h2>

            <p>
                Send denne lenken til personen du vil invitere.
            </p>

            <div class="inviter-lenke-rad">

                <input
                    type="text"
                    id="invitasjonslenke"
                    value="<?php echo htmlspecialchars($generert_lenke); ?>"
                    readonly
                >

                <button
                    type="button"
                    class="button button-secondary"
                    data-kopier-mal="invitasjonslenke"
                >
                    Kopier
                </button>

            </div>

        </div>

    <?php endif; ?>


    <section class="card">

        <h2>Inviter via lenke</h2>

        <p>
            Generer en unik lenke og send den til personen du vil invitere.
        </p>

        <?php echo '<form method="post" action="" class="form">'; ?>

            <?php echo csrf_felt(); ?>

            <button
                type="submit"
                class="button button-primary"
            >
                Generer invitasjonslenke
            </button>

        </form>

    </section>

</section>