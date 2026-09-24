<div class="inviter-til-gruppe-wrapper">

    <?php if ($har_blitt_medlem): ?>

        <section
            class="card"
            aria-labelledby="godkjent-heading"
        >

            <h1 id="godkjent-heading">
                Du er nå medlem
            </h1>

            <p>
                Du er lagt til i
                <strong>
                    <?php echo htmlspecialchars($state["gruppe"]["navn"]); ?>
                </strong>
            </p>

            <?php

            $gruppe_url = url(
                "/gruppe.php?gruppe_id=" .
                $state["gruppe"]["id"] .
                "&section=oppgaver"
            );

            echo '<a class="button button-primary" href="' .
                htmlspecialchars($gruppe_url) .
                '">Gå til gruppen</a>';

            ?>

        </section>

    <?php else: ?>

        <section
            class="card"
            aria-labelledby="invitasjon-heading"
        >

            <h1 id="invitasjon-heading">
                Du er invitert
            </h1>

            <p>
              Du er invitert til å bli med i

                <strong>
                    <?php echo htmlspecialchars($state["gruppe"]["navn"]); ?>
                </strong>.
        </p>

            <?php if (!empty($feil)): ?>

                <ul
                    class="invi*er-til-gruppe-feil"
                    role="alert"
                >

                    <?php foreach ($feil as $melding): ?>

                        <li>
                            <?php echo htmlspecialchars($melding); ?>
                        </li>

                    <?php endforeach; ?>

                </ul>

            <?php endif; ?>

            <?php echo '<form method="post" action="">'; ?>

                <?php echo csrf_felt(); ?>

                <button
                    type="submit"
                    class="button button-primary button-lg"
                >
                    Godkjenn invitasjon
                </button>

            </form>

        </section>

    <?php endif; ?>

</div>