<section>

    <div class="diskusjon-title-row">

        <h1>
            <?php echo htmlspecialchars($state["diskusjon"]["tittel"]); ?>
        </h1>

        <p class="diskusjon-meta">

            Tilhører
            <?php echo htmlspecialchars($state["gruppe"]["navn"]); ?>

            <?php if ($state["oppgave"] !== null): ?>

                , knyttet til oppgaven
                <strong>
                    <?php echo htmlspecialchars($state["oppgave"]["tittel"]); ?>
                </strong>

            <?php elseif ($state["fil"] !== null): ?>

                , knyttet til filen
                <strong>
                    <?php echo htmlspecialchars($state["fil"]["fil_navn"]); ?>
                </strong>

            <?php endif; ?>

        </p>

    </div>


    <?php if (!empty($feil)): ?>

        <ul class="form-feil" role="alert">

            <?php foreach ($feil as $melding): ?>

                <li>
                    <?php echo htmlspecialchars($melding); ?>
                </li>

            <?php endforeach; ?>

        </ul>

    <?php endif; ?>


    <?php if (empty($state["innlegg"])): ?>

        <div class="card">

            <h2>Ingen innlegg ennå</h2>

            <p>
                Start diskusjonen ved å skrive det første innlegget.
            </p>

        </div>

    <?php else: ?>

        <ul class="diskusjon-innlegg-liste">

            <?php foreach ($state["innlegg"] as $post): ?>

                <li>

                    <article class="diskusjon-innlegg">

                        <?php if (!empty($post["forfatter_avatar"])): ?>

                            <img
                                src="<?php echo htmlspecialchars($post["forfatter_avatar"]); ?>"
                                alt="Profilbilde"
                            >

                        <?php endif; ?>


                        <div class="diskusjon-innlegg-innhold">

                            <header class="diskusjon-innlegg-header">

                                <span class="diskusjon-innlegg-forfatter">

                                    <?php echo htmlspecialchars(
                                        $post["forfatter_navn"]
                                    ); ?>

                                </span>

                                <time class="diskusjon-innlegg-tid">

                                    <?php echo htmlspecialchars(
                                        $post["opprettet_på"]
                                    ); ?>

                                </time>

                            </header>


                            <p>
                                <?php echo nl2br(
                                    htmlspecialchars($post["tekst"])
                                ); ?>
                            </p>

                        </div>

                    </article>

                </li>

            <?php endforeach; ?>

        </ul>

    <?php endif; ?>


    

        <label for="nytt_innlegg">
            Skriv et innlegg
        </label>

        <textarea
            id="nytt_innlegg"
            name="tekst"
            rows="4"
            required
        ></textarea>

        <button
            type="submit"
            class="button button-primary button-lg"
        >
            Send
        </button>

    </form>

</section>
