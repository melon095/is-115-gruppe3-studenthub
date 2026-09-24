<section class="diskusjon-side">

    <div class="diskusjon-title-row">

        <h1>
            <?php echo htmlspecialchars(
                $state["diskusjon"]["tittel"]
            ); ?>
        </h1>

        <p class="diskusjon-meta">

            Tilhører

            <?php echo htmlspecialchars(
                $state["gruppe"]["navn"]
            ); ?>

            <?php if (
                $state["oppgave"] !== null
            ): ?>

                , knyttet til oppgaven

                <strong>
                    <?php echo htmlspecialchars(
                        $state["oppgave"]["tittel"]
                    ); ?>
                </strong>

            <?php elseif (
                $state["fil"] !== null
            ): ?>

                , knyttet til filen

                <strong>
                    <?php echo htmlspecialchars(
                        $state["fil"]["fil_navn"]
                    ); ?>
                </strong>

            <?php endif; ?>

        </p>

    </div>


    <?php if (!empty($feil)): ?>

        <ul class="form-feil" role="alert">

            <?php foreach (
                $feil as $melding
            ): ?>

                <li>
                    <?php echo htmlspecialchars(
                        $melding
                    ); ?>
                </li>

            <?php endforeach; ?>

        </ul>

    <?php endif; ?>


    <?php if (
        empty($state["innlegg"])
    ): ?>

        <div class="card diskusjon-empty">

            <h2>
                Ingen innlegg ennå
            </h2>

            <p>
                Start diskusjonen ved å skrive det første innlegget.
            </p>

        </div>

    <?php else: ?>

        <ul class="diskusjon-innlegg-liste">

            <?php foreach (
                $state["innlegg"] as $post
            ): ?>

                <li>

                    <article class="diskusjon-innlegg">

                        <?php if (
                            !empty(
                                $post["forfatter_avatar"]
                            )
                        ): ?>

                            <?php
                            echo '<img class="diskusjon-innlegg-avatar" src="' .
                                htmlspecialchars($post["forfatter_avatar"]) .
                                '" alt="Profilbilde">';
                            ?>

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
                                    htmlspecialchars(
                                        $post["tekst"]
                                    )
                                ); ?>
                            </p>


                            <?php if (
                                $state["er_eier"]
                            ): ?>

                                <?php
                                echo '<form method="post" action="" onsubmit="return confirm(\'Vil du slette dette innlegget?\');">';
                                ?>

                                    <?php echo csrf_felt(); ?>

                                    <input
                                        type="hidden"
                                        name="slett_innlegg"
                                        value="1"
                                    >

                                    <input
                                        type="hidden"
                                        name="innlegg_id"
                                        value="<?php echo (int) $post["innlegg_id"]; ?>"
                                    >

                                    <button
                                        type="submit"
                                        class="button button-warning"
                                    >
                                        Slett innlegg
                                    </button>

                                </form>

                            <?php endif; ?>

                        </div>

                    </article>

                </li>

            <?php endforeach; ?>

        </ul>

    <?php endif; ?>


    <div class="diskusjon-skriv">

        <h2>
            Skriv et innlegg
        </h2>

        <?php
        echo '<form method="post" action="" class="diskusjon-nytt-innlegg form">';
        ?>

            <div class="form-felt">

                <label for="nytt_innlegg">
                    Innlegg
                </label>

                <textarea
                    id="nytt_innlegg"
                    name="tekst"
                    rows="5"
                    placeholder="Skriv innlegget ditt her..."
                    required
                ></textarea>

            </div>

            <button
                type="submit"
                class="button button-primary button-lg"
            >
                Send
            </button>

        </form>

    </div>

</section>