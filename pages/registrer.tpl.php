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

        <form method="post" action="" class="form">

            <div class="form-felt">
                <label for="fornavn">Fornavn</label>
                <input
                    type="text"
                    id="fornavn"
                    name="fornavn"
                    value="<?php echo htmlspecialchars($fornavn); ?>"
                    autocomplete="given-name"
                    required
                >
            </div>

            <div class="form-felt">
                <label for="etternavn">Etternavn</label>
                <input
                    type="text"
                    id="etternavn"
                    name="etternavn"
                    value="<?php echo htmlspecialchars($etternavn); ?>"
                    autocomplete="family-name"
                    required
                >
            </div>

            <div class="form-felt">
                <label for="epost">E-post</label>
                <input
                    type="email"
                    id="epost"
                    name="epost"
                    value="<?php echo htmlspecialchars($epost ?: ""); ?>"
                    autocomplete="email"
                    required
                >
            </div>

            <div class="form-felt">
                <label for="passord">Passord</label>
                <input
                    type="password"
                    id="passord"
                    name="passord"
                    autocomplete="new-password"
                    required
                >
            </div>

            <div class="form-felt">
                <label for="bekreft_passord">Bekreft passord</label>
 <div class="form-felt">
    <label for="bekreft_passord">Bekreft passord</label>

    <input
        type="password"
        id="bekreft_passord"
        name="bekreft_passord"
        autocomplete="new-password"
        required
    >
</div>

<button type="submit" class="button button-primary button-lg">
    Registrer deg
</button>

</form>